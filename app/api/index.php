<?php
    require 'config.php';
    require 'vendor/autoload.php';
    require 'class-api.php';

    $api = new Api();

    function json_response($data) {
        Flight::json(
            $data,
            $code = 200,
            $encode = true,
            $charset = 'utf-8',
            JSON_PARTIAL_OUTPUT_ON_ERROR
        );
    }

    Flight::set('flight.log_errors', DEBUG);
    Flight::set('flight.views.path', './');

    Flight::route('/', function() {
        Flight::render('home.php');
    });

    Flight::route('/address/', function() use ($api) {
        $city = Flight::request()->query->city;

        if (!$city) {
            Flight::halt(400, "No city given");
            die();
        }

        json_response($api->getAddressesByCity($city));
    });

    Flight::route('/address/@id', function($id) use($api) {
        json_response($api->getAddressById($id));
    });

    Flight::route('/film/_videos', function() use ($api) {
        json_response($api->getFilmWithVideo());
    });

    Flight::route('/film/@id', function($id) use($api) {
        json_response($api->getFilmById($id));
    });

    Flight::route('/programme/@id', function($id) use($api) {
        json_response($api->getProgrammeById($id));
    });

    Flight::route('/venue/@id', function($id) use($api) {
        json_response($api->getVenueById($id));
    });

    Flight::start();