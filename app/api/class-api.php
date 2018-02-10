<?php
class Api {
    const TBL_ADDRESS = 'tblAddress';
    const TBL_ADDRESS_LINKS = 'cinemadamAddressLinkIdentifiers';
    const TBL_CENSORSHIP = "tblCensorship";
    const TBL_CENSORSHIP_TITLE = 'tblCensorshipTitle';
    const TBL_CENSORSHIP_SOURCE = 'tblJoinCensorshipArchive';
    const TBL_FILM = 'tblFilm';
    const TBL_FILM_LINKS = 'cinemadamFilmLinkIdentifiers';
    const TBL_FILM_VARIATION = 'tblFilmTitleVariation';
    const TBL_FILM_VIDEO = 'cinemadamFilmVideo';
    const TBL_PROGRAMME = 'tblProgramme';
    const TBL_PROGRAMME_DATE = 'tblProgrammeDate';
    const TBL_PROGRAMME_ITEM = 'tblProgrammeItem';
    const TBL_VENUE = 'tblVenue';
    const TBL_VENUE_ACTIVE = 'tblVenueActivePeriode';
    const TBL_VENUE_SCREENS = 'tblVenueScreen';
    const TBL_VENUE_SEATS = 'tblVenueSeats';

    const ADDRESS_FIELDS = ['address_id', 'street_name', 'geodata', 'info', 'city_name'];
    const FILM_VARIATION_FIELDS = [
        'film_variation_id', 'film_id', 'title', 'language_code', 'info'
    ];
    const VENUE_FIELDS = ['venue_id', 'address_id', 'name', 'venue_type', 'info'];

    function __construct() {
        $this->setupDb();
    }

    public function getAddressesByCity($city) {
        $addresses = ORM::for_table(self::TBL_ADDRESS)
            ->select_many(self::ADDRESS_FIELDS)
            ->where('city_name', $city)
            ->find_array();

        return array_map(function($address) {
            $address['venues'] = $this->getVenuesByAddressId($address['address_id']);
            return $address;
        }, $addresses);
    }

    public function getAddressById($address_id) {
        $address = ORM::for_table(self::TBL_ADDRESS)
            ->select_many(self::ADDRESS_FIELDS)
            ->where('address_id', $address_id)
            ->find_array()[0];

        $address['links'] = $this->getFields(
            self::TBL_ADDRESS_LINKS,
            ['bag_id', 'rm_id', 'wikidata_id', 'vgebouwen_id'],
            ['address_id', $address_id]
        );

        if ($address['links']) {
            $address['links'] = $address['links'][0];
        }

        if (!$address) {
            return ["error" => 404];
        }

        $address['venues'] = $this->getVenuesByAddressId($address_id);

        return $address;
    }

    public function getFilmById($film_id, $expanded = true) {
        // Film variation?
        if (strpos($film_id, ".") !== false) {
            $variation = $this->getFields(
                self::TBL_FILM_VARIATION,
                self::FILM_VARIATION_FIELDS,
                ['film_variation_id', $film_id]
            )[0];

            $film_id = explode(".", $film_id)[0];
        }

        $film = $this->getFields(
            self::TBL_FILM,
            ['film_id', 'title', 'film_year', 'country',
             'film_director', 'film_length', 'film_gauge',
             'info', 'imdb'],
            ["film_id", $film_id]
        );

        if (!$film) {
            return [ "error" => 404 ];
        }

        $film = $film[0];

        if (isset($variation)) {
            $film['variation'] = $variation;
        } else {
            $film['variations'] = $this->getFields(
                self::TBL_FILM_VARIATION,
                self::FILM_VARIATION_FIELDS,
                ['film_id', $film_id]
            );
        }

        if ($expanded) {
            $film['censorship'] = $this->getCensorship($film_id);
            $film['venues'] = $this->getVenuesByFilmId($film_id);

            $film['wikidata'] = $this->getFields(
                self::TBL_FILM_LINKS,
                ['wikidata'],
                ['imdb', "tt" . $film['imdb']]
            );

            if (count($film['wikidata']) > 0) {
                $film['wikidata'] = $film['wikidata'][0]['wikidata'];
            } else {
                $film['wikidata'] = null;
            }
        }
        // Check for full video
        if (isset($film['imdb'])) {
            $imdb = $film['imdb'];
            $hasvideo = ORM::for_table(SELF::TBL_FILM_VIDEO)
                ->where('imdb', "tt$imdb")
                ->find_array();

            $film['hasvideo'] = count($hasvideo) > 0;
        } else {
            $film['hasvideo'] = false;
        }

        return $film;
    }

    public function getProgrammeById($id) {
        $join = ['programme_id', $id];

        $prog = $this->getFields(
            self::TBL_PROGRAMME,
            ['programme_id', 'venue_id', 'programme_info'],
            $join
        );

        if ($prog) {
            $prog = $prog[0];
        }

        $prog['date'] = $this->getFields(
            self::TBL_PROGRAMME_DATE,
            ['programme_date'],
            $join
        );

        if ($prog['date']) {
            $prog['date'] = $prog['date'][0];
        }

        $item = $this->getFields(
            self::TBL_PROGRAMME_ITEM,
            ['film_id', 'film_variation_id'],
            $join
        );

        if ($item) {
            $item = $item[0];
        }

        if ($item) {
            if (isset($item['film_variation_id'])) {
                $prog['film'] = $this->getFilmById($item['film_variation_id'], false);
            } else if (isset($item['film_id'])) {
                $prog['film'] = $this->getFilmById($item['film_id'], false);
            }
        }

        return $prog;
    }

    public function getVenueByAddressId($address_id) {
        $venue = ORM::for_table(self::TBL_VENUE)
            ->select_many(self::VENUE_FIELDS)
            ->where('address_id', $address_id)
            ->find_array()[0];

        return $this->enrichVenue($venue);
    }

    public function getVenuesByAddressId($address_id) {
        $venues = ORM::for_table(self::TBL_VENUE)
            ->select_many(self::VENUE_FIELDS)
            ->where('address_id', $address_id)
            ->find_array();

        return array_map(function($v) {
            return $this->enrichVenue($v);
        }, $venues);
    }

    public function getVenueById($id, $getPrograms = true) {
        $venue = $this->getFields(
            self::TBL_VENUE,
            self::VENUE_FIELDS,
            ['venue_id', $id]
        );

        if (!$venue) {
            return [ "error" => 404 ];
        }

        $venue = $venue[0];

        if ($getPrograms) {
            $venue['programmes'] = array_map(function($p) {
                return $this->getProgrammeById($p['programme_id']);
            }, $this->getFields(
                self::TBL_PROGRAMME,
                ['programme_id'],
                ['venue_id', $id]
            ));
        }

        $venue['address'] = $this->getAddressById($venue['address_id']);

        return $venue;
    }

    public function getVenuesByFilmId($id) {
        $programmes = ORM::for_table(self::TBL_PROGRAMME_ITEM)
            ->select('programme_id')
            ->where_raw("`film_id` = '$id' OR `film_variation_id` LIKE '$id.%'")
            ->find_array();

        return array_map(function($p) {
            $venue_ids = $this->getFields(
                self::TBL_PROGRAMME,
                ['venue_id'],
                ['programme_id', $p['programme_id']]
            );

            return array_map(function($v) {
                return $this->getVenueById($v['venue_id'], false);
            }, $venue_ids)[0];
        }, $programmes);
    }

    private function enrichVenue($venue) {
        $join = ['venue_id', $venue['venue_id']];

        $venue['active'] = $this->getFields(
            self::TBL_VENUE_ACTIVE,
            ['date_opened', 'date_closed'],
            $join
        );

        $venue['screens'] = $this->getFields(
            self::TBL_VENUE_SCREENS,
            ['number_of_screens', 'date_opened', 'info'],
            $join
        );

        $venue['seats'] = $this->getFields(
            self::TBL_VENUE_SEATS,
            ['seats_year', 'number_of_seats'],
            $join
        );

        return $venue;
    }

    private function getCensorship($film_id) {
        $censorship = $this->getFields(
            self::TBL_CENSORSHIP,
            ['censorship_id', 'film_id', 'filing_nr', 'recommendation',
             'censorship_date', 'rating', 'comment_by_censor'],
            ['film_id', $film_id]
        );

        return array_map(function($citem) {
            $citem['title'] = $this->getFields(
                self::TBL_CENSORSHIP_TITLE,
                ['title', 'censorshiptitle_note'],
                ['censorship_id', $citem['censorship_id']]
            );

            $citem['source'] = $this->getFields(
                self::TBL_CENSORSHIP_SOURCE,
                ['info'],
                ['censorship_id', $citem['censorship_id']]
            );

            return $citem;
        }, $censorship);
    }

    private function getFields($table, $fields, $where) {
        return ORM::for_table($table)
            ->select_many($fields)
            ->where($where[0], $where[1])
            ->find_array();
    }

    private function setupDb() {
        ORM::configure([
            "connection_string" => sprintf('mysql:host=%s;dbname=%s;', DB_HOST, DB_DATABASE),
            "username" => DB_USER,
            "password" => DB_PASS,
            "logging" => DEBUG
        ]);

        if (DEBUG) {
            ORM::configure('logger', function($log_string) {
                error_log($log_string);
            });
        }
    }

    private function jsonResponse($data) {
        header("Content-type: application/json");
        $json = json_encode($data);
        die($json);
    }
}