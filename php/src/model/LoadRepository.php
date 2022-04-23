<?php

namespace Model;

class LoadRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllLoads() {
        $result = $this->db->select("
        SELECT id, id_schedule, id_pupil, id_teacher, id_subject 
        FROM `load`
        ");
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'schedule' => $item['id_schedule'],
                'pupil' => $item['id_pupil'],
                'teacher' => $item['id_teacher'],
                'subject' => $item['id_subject'],
            ];
        }, $result);
    }

    public function getOneLoad(int $id) {
        $result = $this->db->select("
        SELECT id, id_schedule, id_pupil, id_teacher, id_subject 
        FROM `load`
        WHERE id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'schedule' => $result[0]['id_schedule'],
            'pupil' => $result[0]['id_pupil'],
            'teacher' => $result[0]['id_teacher'],
            'subject' => $result[0]['id_subject'],
        ];
    }

    public function deleteLoad(int $id) {
        $this->db->executeStatement("
        DELETE FROM `load` WHERE `id` = $id
        ");
    }

    public function updateLoad(int $id, array $data): array {
        $set_values = [];
        if (isset($data['schedule'])) {
            $set_values[] = "`id_schedule` = {$data['schedule']}";
        }
        if (isset($data['pupil'])) {
            $set_values[] = "`id_pupil` = {$data['pupil']}";
        }
        if (isset($data['teacher'])) {
            $set_values[] = "`id_teacher` = {$data['teacher']}";
        }
        if (isset($data['subject'])) {
            $set_values[] = "`id_subject` = {$data['subject']}";
        }
        $set_string = implode(", ", $set_values);
        if (strlen($set_string)) {
            $this->db->executeStatement("
            UPDATE `load`
            SET $set_string
            WHERE `id` = $id
            ");
        }
        return $this->getOneLoad($id);
    }

    public function createLoad(array $data): array {
        $this->db->executeStatement("
        INSERT INTO `load` (`id_schedule`, `id_pupil`, `id_teacher`, `id_subject`) 
        VALUES ({$data['schedule']}, {$data['pupil']}, {$data['teacher']}, {$data['subject']})
        ");
        $new_id = $this->db->lastId();
        return $this->getOneLoad($new_id);
    }
}