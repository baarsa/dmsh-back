<?php

namespace Model;

class SubjectRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllSubjects() {
        $result = $this->db->select("
        SELECT s.id, s.name, ss.id_speciality_group
        FROM `subject` s 
        LEFT JOIN `special_subject` ss ON ss.id_subject = s.id
        ");
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'isSpecial' => $item['id_speciality_group'] !== null,
                'specialityGroup' => $item['id_speciality_group'],
            ];
        }, $result);
    }

    public function getOneSubject(int $id) {
        $result = $this->db->select("
        SELECT s.id, s.name, ss.id_speciality_group
        FROM `subject` s 
        LEFT JOIN `special_subject` ss ON ss.id_subject = s.id
        WHERE s.id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'name' => $result[0]['name'],
            'isSpecial' => $result[0]['id_speciality_group'] !== null,
            'specialityGroup' => $result[0]['id_speciality_group'],
        ];
    }

    public function deleteSubject(int $id) {
        $this->db->executeStatement("
        DELETE FROM `subject` WHERE `id` = $id
        ");
    }

    public function updateSubject(int $id, array $data) {
        //todo transactions
        $this->db->executeStatement("
        UPDATE `subject`
        SET `name` = \"{$data['name']}\"
        WHERE `id` = $id
        ");
        $this->db->executeStatement("DELETE FROM `special_subject` WHERE `id_subject` = $id");
        if ($data['isSpecial']) {
            $this->db->executeStatement("
            INSERT INTO `special_subject` (`id_subject`, `id_speciality_group`) VALUES ($id, {$data['specialityGroup']})
            ");
        }
    }

    public function createSubject(array $data): int {
        $this->db->executeStatement("
        INSERT INTO `subject` (`name`) VALUES (\"{$data['name']}\")
        ");
        $new_id = $this->db->lastId();
        if ($data['isSpecial']) {
            $this->db->executeStatement("
            INSERT INTO `special_subject` (`id_subject`, `id_speciality_group`) VALUES ($new_id, {$data['specialityGroup']})
        ");
        }
        return $new_id;
    }
}