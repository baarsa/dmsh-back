<?php
namespace Config;

class Config {
    public string $DB_HOST;
    public string $DB_USERNAME;
    public string $DB_PASSWORD;
    public string $DB_DATABASE_NAME;

    public function __construct()
    {
        $this->DB_HOST = getenv("DB_HOST");
        $this->DB_USERNAME = getenv("DB_USERNAME");
        $this->DB_PASSWORD = getenv("DB_PASSWORD");
        $this->DB_DATABASE_NAME = getenv("DB_DATABASE_NAME");
    }
}
