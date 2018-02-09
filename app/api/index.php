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

    $app->get('/address/', function (Request $request, Response $response, array $args) {
        global $api;
        $addresses = $api->address();
        $json = $response->withJson($addresses);
        return $json;
    });

    $app->get('/address/{address_id}', function (Request $request, Response $response, array $args) {
        global $api;
        $address = $api->getAddressById($args['address_id']);
        $json = $response->withJson($address);
        return $json;
    });

    $app->run();