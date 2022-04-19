<?php

use Controller\ExtraEmploymentController;
use Controller\TeacherController;
use Model\Database;
use Model\ExtraEmploymentRepository;
use Model\TeacherRepository;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(false, false, false);

$db = new Database();

$teacherController = new TeacherController($app, new TeacherRepository($db));
$extraEmploymentController = new ExtraEmploymentController($app, new ExtraEmploymentRepository($db));

$app->run();
