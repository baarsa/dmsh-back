<?php

namespace Controller;

use Model\LessonRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
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

    public function deleteAssistance(int $id)
    {
        return $this->lessonRepository->deleteAssistance($id);
    }

    public function update(int $id, array $data)
    {
        return $this->lessonRepository->updateLesson($id, $data);
    }

    public function __construct(App $app, LessonRepository $lessonRepository)
    {
        parent::__construct($app);
        $app->get("/api/" . static::$RESOURCE_NAME . "/{id}/delete-assistance", function (Request $request, Response $response, $args) {
            $resource_id = (int)$args['id'];
            $updated_object = $this->deleteAssistance($resource_id, $request->getParsedBody());
            $response->getBody()->write(json_encode($updated_object));
            return $response->withHeader('Content-Type', 'application/json');
        });
        $this->lessonRepository = $lessonRepository;
    }
}