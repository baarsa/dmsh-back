<?php

namespace Controller;

use Model\PupilRepository;
use Slim\App;

class PupilController extends Controller
{
    protected static string $RESOURCE_NAME = "pupil";
    private readonly PupilRepository $pupil;

    public function getAll(): array
    {
        return $this->pupil->getAllPupils();
    }

    public function getOne(int $id): array
    {
        return $this->pupil->getOnePupil($id);
    }

    public function create(array $data)
    {
        return $this->pupil->createPupil($data);
    }

    public function delete(int $id)
    {
        $this->pupil->deletePupil($id);
    }

    public function update(int $id, array $data)
    {
        $this->pupil->updatePupil($id, $data);
    }

    public function __construct(App $app, PupilRepository $pupil)
    {
        parent::__construct($app);
        $this->pupil = $pupil;
    }
}