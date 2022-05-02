<?php

namespace Controller;

use Model\ScheduleRepository;
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

    public function __construct(App $app, ScheduleRepository $schedule)
    {
        parent::__construct($app);
        $this->schedule = $schedule;
    }
}