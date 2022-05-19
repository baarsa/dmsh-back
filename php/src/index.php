<?php

use Controller\AuthController;
use Controller\ExtraEmploymentController;
use Controller\GroupController;
use Controller\FrontController;
use Controller\LessonController;
use Controller\LoadController;
use Controller\ProgramController;
use Controller\PupilController;
use Controller\ScheduleController;
use Controller\SpecialityGroupController;
use Controller\SubjectController;
use Controller\TeacherController;
use Controller\UserController;
use Model\AuthManager;
use Model\Database;
use Model\DataUploader;
use Model\ExtraEmploymentRepository;
use Model\GroupRepository;
use Model\LessonRepository;
use Model\LoadRepository;
use Model\ProgramRepository;
use Model\PupilParser;
use Model\PupilRepository;
use Model\ScheduleRepository;
use Model\SpecialityGroupRepository;
use Model\SubjectRepository;
use Model\TeacherParser;
use Model\TeacherRepository;
use Model\UserRepository;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

session_start();
const UPLOAD_DIR = __DIR__ . '/uploads';
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR);
}
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(false, false, false);
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5001')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

$db = new Database();
$uploader = new DataUploader();

$teacherController = new TeacherController($app, new TeacherRepository($db), $uploader, new TeacherParser($db));
$extraEmploymentController = new ExtraEmploymentController($app, new ExtraEmploymentRepository($db));
$groupController = new GroupController($app, new GroupRepository($db));
$lessonController = new LessonController($app, new LessonRepository($db));
$loadController = new LoadController($app, new LoadRepository($db));
$programController = new ProgramController($app, new ProgramRepository($db));
$pupilController = new PupilController($app, new PupilRepository($db), $uploader, new PupilParser($db));
$scheduleController = new ScheduleController($app, new ScheduleRepository($db));
$specialityGroupController = new SpecialityGroupController($app, new SpecialityGroupRepository($db));
$subjectController = new SubjectController($app, new SubjectRepository($db));
$userController = new UserController($app, new UserRepository($db));
$authController = new AuthController($app, new AuthManager($db));
$frontController = new FrontController($app);

$app->run();
