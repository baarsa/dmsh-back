<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

abstract class Controller
{
    protected  static string $RESOURCE_NAME;

    abstract function getAll(): array;
    abstract function getOne(int $id): array;
    abstract function create(array $data);
    abstract function delete(int $id);
    abstract function update(int $id, array $data);

    public function __construct(App $app) {
        $app->get("/" . static::$RESOURCE_NAME . "/all", function (Request $request, Response $response) {
            $response->getBody()->write(json_encode($this->getAll()));
            return $response;
        });
        $app->get(static::$RESOURCE_NAME . "/{id}", function (Request $request, Response $response, $args) {
            $resource_id = (int)$args['id'];
            $response->getBody()->write(json_encode($this->getOne($resource_id)));
            return $response;
        });
        $app->post(static::$RESOURCE_NAME, function (Request $request, Response $response) {
            $this->create($request->getParsedBody());
            return $response;
        });
        $app->post(static::$RESOURCE_NAME . "/{id}", function (Request $request, Response $response, $args) {
            $resource_id = (int)$args['id'];
            $this->update($resource_id, $request->getParsedBody());
            return $response;
        });
        $app->delete(static::$RESOURCE_NAME . "/{id}", function (Request $request, Response $response, $args) {
            $resource_id = (int)$args['id'];
            $this->delete($resource_id);
            return $response;
        });
    }
}