<?php

namespace Controller;

use Model\AuthManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

class AuthController
{
    public function __construct(App $app, AuthManager $auth) {
        $app->get("/api/auth", function (Request $request, Response $response) use ($auth) {
            $response->getBody()->write(json_encode($auth->auth()));
            return $response;
        });
        $app->post("/api/login", function (Request $request, Response $response) use ($auth) {
            ['username' => $login, 'password' => $password] = $request->getParsedBody();
            $response->getBody()->write(json_encode($auth->login($login, $password)));
            return $response;
        });
        $app->get("/api/logout", function (Request $request, Response $response) use ($auth) {
            $auth->logout();
            return $response;
        });
    }
}