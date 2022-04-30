<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

class FrontController
{
    public function __construct(App $app) {
        $app->get("/[{path}]", function (Request $request, Response $response, $args) {
            $path = array_key_exists('path', $args) ? $args['path'] : '';
            $front_response = file_get_contents("http://localhost:5001/$path");
            $response->getBody()->write($front_response);
            return $response;
        });
    }
}