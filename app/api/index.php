<?php
    require 'config.php';
    require 'vendor/autoload.php';
    require 'class-api.php';

    $api = new Api();

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

        Flight::json($api->getAddressesByCity($city));
    });

    Flight::route('/address/@id', function($id) use($api) {
        Flight::json($api->getAddressById($id));
    });

    Flight::route('/film/_videos', function() use ($api) {
        Flight::json($api->getFilmWithVideo());
    });

    Flight::route('/film/@id', function($id) use($api) {
        Flight::json($api->getFilmById($id));
    });

    Flight::route('/programme/@id', function($id) use($api) {
        Flight::json($api->getProgrammeById($id));
    });

    Flight::route('/venue/@id', function($id) use($api) {
        Flight::json($api->getVenueById($id));
    });

    Flight::start();