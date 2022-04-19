<?php

namespace Controller;

use Model\GroupRepository;
use Slim\App;

class GroupController extends Controller
{
    protected static string $RESOURCE_NAME = "group";
    private readonly GroupRepository $groupRepository;

    public function getAll(): array
    {
        return $this->groupRepository->getAllGroups();
    }

    public function getOne(int $id): array
    {
        return $this->groupRepository->getOneGroup($id);
    }

    public function create(array $data)
    {
        return $this->groupRepository->createGroup($data);
    }

    public function delete(int $id)
    {
        return $this->groupRepository->deleteGroup($id);
    }

    public function update(int $id, array $data)
    {
        return $this->groupRepository->updateGroup($id, $data);
    }

    public function __construct(App $app, GroupRepository $groupRepository)
    {
        parent::__construct($app);
        $this->groupRepository = $groupRepository;
    }
}