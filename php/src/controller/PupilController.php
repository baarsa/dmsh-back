<?php

namespace Controller;

use Model\DataUploader;
use Model\PupilParser;
use Model\PupilRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

class PupilController extends Controller
{
    protected static string $RESOURCE_NAME = "pupil";
    private readonly PupilRepository $pupil;
    private readonly DataUploader $uploader;
    private readonly PupilParser $parser;

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
        return $this->pupil->updatePupil($id, $data);
    }

    public function processFile($file) {
        $data = $this->uploader->getFileData($file);
        $parsed_data = $this->parser->parse($data);
        $this->pupil->createMultiplePupils($parsed_data);
    }

    public function __construct(App $app, PupilRepository $pupil, DataUploader $uploader, PupilParser $parser)
    {
        $app->post("/api/" . static::$RESOURCE_NAME . "/upload", function (Request $request, Response $response, $args) {
            $files = $request->getUploadedFiles();
            $this->processFile($files['file']);
            return $response;
        });
        parent::__construct($app);
        $this->pupil = $pupil;
        $this->uploader = $uploader;
        $this->parser = $parser;
    }
}