<?php
class Api {
    const CITY = "Amsterdam";
    const TBL_ADDRESS = 'tblAddress';
    const TBL_VENUE = 'tblVenue';
    const TBL_VENUE_ACTIVE = 'tblVenueActivePeriode';
    const TBL_VENUE_SCREENS = 'tblVenueScreen';
    const TBL_VENUE_SEATS = 'tblVenueSeats';
    const ADDRESS_FIELDS = ['address_id', 'street_name', 'geodata', 'info'];
    const VENUE_FIELDS = ['venue_id', 'address_id', 'name', 'venue_type', 'info'];

    function __construct() {
        $this->setupDb();
    }

    public function address($withVenues = true) {
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