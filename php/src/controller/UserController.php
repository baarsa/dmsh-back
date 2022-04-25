<?php

namespace Controller;

use Model\UserRepository;
use Slim\App;

class UserController extends Controller
{
    protected static string $RESOURCE_NAME = "user";
    private readonly UserRepository $user;

    public function getAll(): array
    {
        return $this->user->getAllUsers();
    }

    public function getOne(int $id): array
    {
        return $this->user->getOneUser($id);
    }

    public function create(array $data)
    {
        return $this->user->createUser($data);
    }

    public function delete(int $id)
    {
        $this->user->deleteUser($id);
    }

    public function update(int $id, array $data)
    {
        return $this->user->updateUser($id, $data);
    }

    public function __construct(App $app, UserRepository $user)
    {
        parent::__construct($app);
        $this->user = $user;
    }
}