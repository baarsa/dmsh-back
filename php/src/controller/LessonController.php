<?php

namespace Controller;

use Model\LessonRepository;
use Slim\App;

class LessonController extends Controller
{
    protected static string $RESOURCE_NAME = "lesson";
    private readonly LessonRepository $lessonRepository;

    public function getAll(): array
    {
        return $this->lessonRepository->getAllLessons();
    }

    public function getOne(int $id): array
    {
        return $this->lessonRepository->getOneLesson($id);
    }

    public function create(array $data)
    {
        return $this->lessonRepository->createLesson($data);
    }

    public function delete(int $id)
    {
        return $this->lessonRepository->deleteLesson($id);
    }

    public function update(int $id, array $data)
    {
        return $this->lessonRepository->updateLesson($id, $data);
    }

    public function __construct(App $app, LessonRepository $lessonRepository)
    {
        parent::__construct($app);
        $this->lessonRepository = $lessonRepository;
    }
}