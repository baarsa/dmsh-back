<?php

namespace Controller;

use Model\ExtraEmploymentRepository;
use Slim\App;

class ExtraEmploymentController extends Controller
{
    protected static string $RESOURCE_NAME = "extra-employment";
    private readonly ExtraEmploymentRepository $extraEmploymentRepository;

    public function getAll(): array
    {
        return $this->extraEmploymentRepository->getAllExtraEmployments();
    }

    public function getOne(int $id): array
    {
        return $this->extraEmploymentRepository->getOneExtraEmployment($id);
    }

    public function create(array $data)
    {
        return $this->extraEmploymentRepository->createExtraEmployment($data);
    }

    public function delete(int $id)
    {
        $this->extraEmploymentRepository->deleteExtraEmployment($id);
    }

    public function update(int $id, array $data)
    {
        return $this->extraEmploymentRepository->updateExtraEmployment($id, $data);
    }

    public function __construct(App $app, ExtraEmploymentRepository $extraEmploymentRepository)
    {
        parent::__construct($app);
        $this->extraEmploymentRepository = $extraEmploymentRepository;
    }
}