<?php

use Controller\TeacherController;
use Model\Database;
use Model\Teacher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(false, false, false);

$db = new Database();

$teacherController = new TeacherController($app, new Teacher($db));

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello worlda!");
    return $response;
});

$app->run();
