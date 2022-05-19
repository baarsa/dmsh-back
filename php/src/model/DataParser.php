<?php

namespace Model;

abstract class DataParser
{
    protected Database $db;

    abstract function parse($array);

    protected function getNameDictionary($data) {
        $result = [];
        foreach ($data as $item) {
            $result[$item["name"]] = $item;
        }
        return $result;
    }

    public function __construct(Database $db) {
        $this->db = $db;
    }
}