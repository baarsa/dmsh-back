<?php

namespace Model;

class SpecialityGroupRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllSpecialityGroups() {
        $result = $this->db->select("
        SELECT id, name
        FROM `speciality_group`
        ");
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        }, $result);
    }

    public function getOneSpecialityGroup(int $id) {
        $result = $this->db->select("
        SELECT id, name
        FROM `speciality_group`
        WHERE id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'name' => $result[0]['name'],
        ];
    }
}