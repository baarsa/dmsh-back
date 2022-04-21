<?php

namespace Controller;

use Model\SubjectRepository;
use Slim\App;

class SubjectController extends Controller
{
    protected static string $RESOURCE_NAME = "subject";
    private readonly SubjectRepository $subject;

    public function getAll(): array
    {
        return $this->subject->getAllSubjects();
    }

    public function getOne(int $id): array
    {
        return $this->subject->getOneSubject($id);
    }

    public function create(array $data)
    {
        return $this->subject->createSubject($data);
    }

    public function delete(int $id)
    {
        return $this->subject->deleteSubject($id);
    }

    public function update(int $id, array $data)
    {
        return $this->subject->updateSubject($id, $data);
    }

    public function __construct(App $app, SubjectRepository $subject)
    {
        parent::__construct($app);
        $this->subject = $subject;
    }
}