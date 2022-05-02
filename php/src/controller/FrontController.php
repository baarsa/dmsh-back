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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $url = getenv("DEV") == "1" ? "http://dmsh-front:5001" : "http://localhost:5001";
            curl_setopt($ch, CURLOPT_URL, "$url/$path");
            $front_response = curl_exec($ch);
            $response->getBody()->write($front_response);
            return $response;
        });
    }
}