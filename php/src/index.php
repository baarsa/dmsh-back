<?php

use Controller\ExtraEmploymentController;
use Controller\GroupController;
use Controller\LessonController;
use Controller\LoadController;
use Controller\ProgramController;
use Controller\PupilController;
use Controller\ScheduleController;
use Controller\TeacherController;
use Model\Database;
use Model\ExtraEmploymentRepository;
use Model\GroupRepository;
use Model\LessonRepository;
use Model\LoadRepository;
use Model\ProgramRepository;
use Model\PupilRepository;
use Model\ScheduleRepository;
use Model\TeacherRepository;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(false, false, false);

$db = new Database();

$teacherController = new TeacherController($app, new TeacherRepository($db));
$extraEmploymentController = new ExtraEmploymentController($app, new ExtraEmploymentRepository($db));
$groupController = new GroupController($app, new GroupRepository($db));
$lessonController = new LessonController($app, new LessonRepository($db));
$loadController = new LoadController($app, new LoadRepository($db));
$programController = new ProgramController($app, new ProgramRepository($db));
$pupilController = new PupilController($app, new PupilRepository($db));
$scheduleController = new ScheduleController($app, new ScheduleRepository($db));

$app->run();
