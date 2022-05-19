<?php

namespace Controller;

use Model\DataUploader;
use Model\TeacherParser;
use Model\TeacherRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

class TeacherController extends Controller
{
    protected static string $RESOURCE_NAME = "teacher";
    private readonly TeacherRepository $teacher;
    private readonly DataUploader $uploader;
    private readonly TeacherParser $parser;

    public function getAll(): array
    {
        return $this->teacher->getAllTeachers();
    }

    public function getOne(int $id): array
    {
        return $this->teacher->getOneTeacher($id);
    }

    public function create(array $data)
    {
        return $this->teacher->createTeacher($data);
    }

    public function delete(int $id)
    {
        return $this->teacher->deleteTeacher($id);
    }

    public function update(int $id, array $data)
    {
        return $this->teacher->updateTeacher($id, $data);
    }

    public function processFile($file) {
        $data = $this->uploader->getFileData($file);
        $parsed_data = $this->parser->parse($data);
        $this->teacher->createMultipleTeachers($parsed_data);
    }

    public function __construct(App $app, TeacherRepository $teacher, DataUploader $uploader, TeacherParser $parser)
    {
        $app->post("/api/" . static::$RESOURCE_NAME . "/upload", function (Request $request, Response $response, $args) {
            $files = $request->getUploadedFiles();
            $this->processFile($files['file']);
            return $response;
        });
        parent::__construct($app);
        $this->teacher = $teacher;
        $this->uploader = $uploader;
        $this->parser = $parser;
    }
}