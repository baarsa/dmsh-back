<?php

namespace Controller;

use Model\ProgramRepository;
use Slim\App;

class ProgramController extends Controller
{
    protected static string $RESOURCE_NAME = "program";
    private readonly ProgramRepository $programRepository;

    public function getAll(): array
    {
        return $this->programRepository->getAllPrograms();
    }

    public function getOne(int $id): array
    {
        return $this->programRepository->getOneProgram($id);
    }

    public function create(array $data)
    {
        return $this->programRepository->createProgram($data);
    }

    public function delete(int $id)
    {
        return $this->programRepository->deleteProgram($id);
    }

    public function update(int $id, array $data)
    {
        $this->programRepository->updateProgram($id, $data);
    }

    public function __construct(App $app, ProgramRepository $programRepository)
    {
        parent::__construct($app);
        $this->programRepository = $programRepository;
    }
}