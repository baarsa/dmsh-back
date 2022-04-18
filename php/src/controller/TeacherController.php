<?php

namespace Controller;

use Model\Teacher;
use Slim\App;

class TeacherController extends Controller
{
    protected static string $RESOURCE_NAME = "teacher";
    private readonly Teacher $teacher;

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

    public function __construct(App $app, Teacher $teacher)
    {
        parent::__construct($app);
        $this->teacher = $teacher;
    }
}