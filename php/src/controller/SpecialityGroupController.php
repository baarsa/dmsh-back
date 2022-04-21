<?php

namespace Controller;

use Model\SpecialityGroupRepository;
use Slim\App;

class SpecialityGroupController extends Controller
{
    protected static string $RESOURCE_NAME = "speciality-group";
    private readonly SpecialityGroupRepository $specialityGroupRepository;

    public function getAll(): array
    {
        return $this->specialityGroupRepository->getAllSpecialityGroups();
    }

    public function getOne(int $id): array
    {
        return $this->specialityGroupRepository->getOneSpecialityGroup($id);
    }
    
    public function create(array $data)
    {
        
    }

    public function update(int $id, array $data)
    {

    }

    public function delete(int $id)
    {

    }

    public function __construct(App $app, SpecialityGroupRepository $specialityGroupRepository)
    {
        parent::__construct($app);
        $this->specialityGroupRepository = $specialityGroupRepository;
    }
}