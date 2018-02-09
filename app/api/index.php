<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    require 'config.php';
    require 'vendor/autoload.php';
    require 'class-api.php';

    $app = new \Slim\App([
        'settings' => [
            'debug' => DEBUG,
            'displayErrorDetails' => DEBUG
        ]
    ]);
    $api = new Api();

    $app->get("/", function (Request $request, Response $response, array $args) {
        $index = file_get_contents("home.html");
        $response->getBody()->write($index);
        return $response;
    });

    $app->get('/address/', function (Request $request, Response $response, array $args) {
        global $api;
        $addresses = $api->getAllAddresses();
        $json = $response->withJson($addresses);
        return $json;
    });

    $app->get('/address/{address_id}', function (Request $request, Response $response, array $args) {
        global $api;
        $address = $api->getAddressById($args['address_id']);
        $json = $response->withJson($address);
        return $json;
    });

    $app->get('/film/{film_id}', function (Request $request, Response $response, array $args) {
        global $api;
        $film = $api->getFilmById($args['film_id']);
        $json = $response->withJson($film);
        return $json;
    });

    $app->get('/programme/{programme_id}', function (Request $request, Response $response, array $args) {
        global $api;
        $programme = $api->getProgrammeById($args['programme_id']);
        $json = $response->withJson($programme);
        return $json;
    });

    $app->get('/venue/{venue_id}', function (Request $request, Response $response, array $args) {
        global $api;
        $venue = $api->getVenueById($args['venue_id']);
        $json = $response->withJson($venue);
        return $json;
    });

    $app->run();