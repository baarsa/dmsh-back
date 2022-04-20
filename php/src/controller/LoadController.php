<?php

namespace Controller;

use Model\LoadRepository;
use Slim\App;

class LoadController extends Controller
{
    protected static string $RESOURCE_NAME = "load";
    private readonly LoadRepository $loadRepository;

    public function getAll(): array
    {
        return $this->loadRepository->getAllLoads();
    }

    public function getOne(int $id): array
    {
        return $this->loadRepository->getOneLoad($id);
    }

    public function create(array $data)
    {
        return $this->loadRepository->createLoad($data);
    }

    public function delete(int $id)
    {
        return $this->loadRepository->deleteLoad($id);
    }

    public function update(int $id, array $data)
    {
        return $this->loadRepository->updateLoad($id, $data);
    }

    public function __construct(App $app, LoadRepository $loadRepository)
    {
        parent::__construct($app);
        $this->loadRepository = $loadRepository;
    }
}