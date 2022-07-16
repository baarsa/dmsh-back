<?php

namespace Controller;

use Model\ScheduleRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

class ScheduleController extends Controller
{
    protected static string $RESOURCE_NAME = "schedule";
    private readonly ScheduleRepository $schedule;

    public function getAll(): array
    {
        return $this->schedule->getAllSchedules();
    }

    public function getOne(int $id): array
    {
        return $this->schedule->getOneSchedule($id);
    }

    public function create(array $data)
    {
        return $this->schedule->createSchedule($data);
    }

    public function delete(int $id)
    {
        $this->schedule->deleteSchedule($id);
    }

    public function update(int $id, array $data)
    {
        return $this->schedule->updateSchedule($id, $data);
    }

    public function copy(int $id, string $name, bool $next_year)
    {
        return $this->schedule->copySchedule($id, $name, $next_year);
    }

    public function __construct(App $app, ScheduleRepository $schedule)
    {
        $app->post("/api/" . static::$RESOURCE_NAME . "/copy", function (Request $request, Response $response) {
            ['id' => $id, 'name' => $name, 'nextYear' => $next_year] = $request->getParsedBody();
            $new_schedule = $this->copy($id, $name, $next_year);
            $response->getBody()->write(json_encode($new_schedule));
            return $response->withHeader('Content-Type', 'application/json');
        });
        parent::__construct($app);
        $this->schedule = $schedule;
    }
}