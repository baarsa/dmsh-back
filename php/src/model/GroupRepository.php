<?php

namespace Model;

class GroupRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllGroups() {
        $result = $this->db->select("
        SELECT lg.id, lg.id_lesson_taker, lg.name, plg.id_pupil 
        FROM `learning_group` lg 
        JOIN `pupil_learning_group` plg ON plg.id_learning_group = lg.id
        ");
        return array_values(array_reduce($result, function($acc, $item) {
            if (!array_key_exists($item['id'], $acc)) {
                $acc[$item['id']] = [
                    'id' => $item['id'],
                    'lessonTakerId' => $item['id_lesson_taker'],
                    'name' => $item['name'],
                    'pupils' => [$item['id_pupil']],
                ];
            } else {
                $acc[$item['id']]['pupils'][] = $item['id_pupil'];
            }
            return $acc;
        }, []));
    }

    public function getOneGroup(int $id) {
        $result = $this->db->select("
        SELECT lg.id, lg.id_lesson_taker, lg.name, plg.id_pupil 
        FROM `learning_group` lg 
        JOIN `pupil_learning_group` plg ON plg.id_learning_group = lg.id
        WHERE lg.id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'lessonTakerId' => $result[0]['id_lesson_taker'],
            'name' => $result[0]['name'],
            'pupils' => array_map(function ($item) {
                return $item['id_pupil'];
            }, $result),
        ];
    }

    public function deleteGroup(int $id) {
        $this->db->executeStatement("
        DELETE FROM `pupil_learning_group` WHERE `id_learning_group` = $id
        ");
        $id_lesson_taker = $this->db->getOneValue("SELECT `id_lesson_taker` FROM `learning_group` WHERE `id` = $id");
        $this->db->executeStatement("
        DELETE FROM `learning_group` WHERE `id` = $id
        ");
        $this->db->executeStatement("
        DELETE FROM `lesson_taker` WHERE `id` = $id_lesson_taker
        ");
    }

    public function updateGroup(int $id, array $data): array {
        $this->db->executeStatement("
        UPDATE `learning_group`
        SET `name` = \"{$data['name']}\"
        WHERE `id` = $id
        ");
        $this->db->executeStatement("DELETE FROM `pupil_learning_group` WHERE id_learning_group = $id");
        $values_string = implode(", ", array_map(function ($pupil) use ($id) {
            return "($id, $pupil)";
        }, $data['pupils']));
        $this->db->executeStatement("
        INSERT INTO `pupil_learning_group` (`id_learning_group`, `id_pupil`) 
        VALUES $values_string
        ");
        return $this->getOneGroup($id);
    }

    public function createGroup(array $data): array {
        $this->db->executeStatement("
        INSERT INTO `lesson_taker` () VALUES ()
        ");
        $new_lesson_taker_id = $this->db->lastId();
        $this->db->executeStatement("
        INSERT INTO `learning_group` (`id_lesson_taker`, `name`)
        VALUES ($new_lesson_taker_id, \"{$data['name']}\")
        ");
        $new_id = $this->db->lastId();
        $values_string = implode(", ", array_map(function ($pupil) use ($new_id) {
            return "($new_id, $pupil)";
        }, $data['pupils']));
        $this->db->executeStatement("
        INSERT INTO `pupil_learning_group` (`id_learning_group`, `id_pupil`) 
        VALUES $values_string
        ");
        return $this->getOneGroup($new_id);
    }
}