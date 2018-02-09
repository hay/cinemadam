<?php
class Api {
    const CITY = "Amsterdam";
    const TBL_ADDRESS = 'tblAddress';
    const TBL_VENUE = 'tblVenue';
    const ADDRESS_FIELDS = ['address_id', 'street_name', 'geodata', 'info'];

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
            ->find_array();

        $address['venues'] = $this->getVenuesByAddressId($address_id);

        return $address;
    }

    public function getVenuesByAddressId($address_id) {
        return ORM::for_table(self::TBL_VENUE)
            ->select_many('venue_id', 'address_id', 'name', 'venue_type', 'info')
            ->where('address_id', $address_id)
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