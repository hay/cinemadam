<?php
class Api {
    const CITY = "Amsterdam";
    const TBL_ADDRESS = 'tblAddress';
    const TBL_FILM = 'tblFilm';
    const TBL_FILM_VARIATION = 'tblFilmTitleVariation';
    const TBL_PROGRAMME = 'tblProgramme';
    const TBL_PROGRAMME_DATE = 'tblProgrammeDate';
    const TBL_PROGRAMME_ITEM = 'tblProgrammeItem';
    const TBL_VENUE = 'tblVenue';
    const TBL_VENUE_ACTIVE = 'tblVenueActivePeriode';
    const TBL_VENUE_SCREENS = 'tblVenueScreen';
    const TBL_VENUE_SEATS = 'tblVenueSeats';

    const ADDRESS_FIELDS = ['address_id', 'street_name', 'geodata', 'info'];
    const FILM_VARIATION_FIELDS = [
        'film_variation_id', 'film_id', 'title', 'language_code', 'info'
    ];
    const VENUE_FIELDS = ['venue_id', 'address_id', 'name', 'venue_type', 'info'];

    function __construct() {
        $this->setupDb();
    }

    public function getAllAddresses($withVenues = true) {
        $addresses = ORM::for_table(self::TBL_ADDRESS)
            ->select_many(self::ADDRESS_FIELDS)
            ->where('city_name', self::CITY)
            ->find_array();

        if ($withVenues) {
            return array_map(function($address) {
                $address['venues'] = $this->getVenuesByAddressId($address['address_id']);
                return $address;
            }, $addresses);
        } else {
            return $addresses;
        }
    }

    public function getAddressById($address_id) {
        $address = ORM::for_table(self::TBL_ADDRESS)
            ->select_many(self::ADDRESS_FIELDS)
            ->where('address_id', $address_id)
            ->find_array()[0];

        if (!$address) {
            return ["error" => 404];
        }

        $address['venues'] = $this->getVenuesByAddressId($address_id);

        return $address;
    }

    public function getFilmById($film_id) {
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
             'info'],
            ["film_id", $film_id]
        )[0];

        if ($variation) {
            $film['variation'] = $variation;
        } else {
            $film['variations'] = $this->getFields(
                self::TBL_FILM_VARIATION,
                self::FILM_VARIATION_FIELDS,
                ['film_id', $film_id]
            );
        }

        return $film;
    }

    public function getProgrammeById($id) {
        $join = ['programme_id', $id];

        $prog = $this->getFields(
            self::TBL_PROGRAMME,
            ['programme_id', 'venue_id'],
            $join
        )[0];

        $prog['date'] = $this->getFields(
            self::TBL_PROGRAMME_DATE,
            ['programme_date'],
            $join
        )[0];

        $item = $this->getFields(
            self::TBL_PROGRAMME_ITEM,
            ['film_variation_id'],
            $join
        )[0];

        if ($item) {
            $prog['film'] = $this->getFilmById($item['film_variation_id']);
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

    public function getVenueById($id) {
        $venue = $this->getFields(
            self::TBL_VENUE,
            self::VENUE_FIELDS,
            ['venue_id', $id]
        )[0];

        $venue['programmes'] = array_map(function($p) {
            return $this->getProgrammeById($p['programme_id']);
        }, $this->getFields(
            self::TBL_PROGRAMME,
            ['programme_id'],
            ['venue_id', $id]
        ));

        return $venue;
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
            "password" => DB_PASS
        ]);
    }

    private function jsonResponse($data) {
        header("Content-type: application/json");
        $json = json_encode($data);
        die($json);
    }
}