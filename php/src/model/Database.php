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
        try {
            $this->connection = new \mysqli(Config::$DB_HOST, Config::$DB_USERNAME, Config::$DB_PASSWORD, Config::$DB_DATABASE_NAME);

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
}