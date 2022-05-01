<?php
namespace Model;
use Exception;
use mysqli;
use Config\Config;

class Database
{
    protected mysqli $connection;

    public function __construct()
    {
        $config = new Config();
        try {
            $this->connection = new \mysqli($config->DB_HOST, $config->DB_USERNAME, $config->DB_PASSWORD, $config->DB_DATABASE_NAME);
            $this->connection->set_charset("UTF8");

            if (mysqli_connect_errno()) {
                throw new Exception("Не удалось подключиться к БД");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function select($query = "" , $params = []): array
    {
        try {
            $stmt = $this->executeStatement( $query , $params );
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $result;
        } catch(Exception $e) {
            throw new Exception( $e->getMessage() );
        }
    }

    public function executeStatement($query = "" , $params = []): \mysqli_stmt
    {
        try {
            $stmt = $this->connection->prepare( $query );

            if($stmt === false) {
                throw new Exception("Невозможно обработать запрос: " . $query);
            }

            if( $params ) {
                $stmt->bind_param($params[0], $params[1]);
            }

            $stmt->execute();

            return $stmt;
        } catch(Exception $e) {
            throw new Exception( $e->getMessage() );
        }
    }

    public function lastId(): int
    {
        return $this->connection->insert_id;
    }

    public function getIds($query): array
    {
        $result = $this->select($query);
        return array_map(function ($item) {
            return $item['id'];
        }, $result);
    }
}