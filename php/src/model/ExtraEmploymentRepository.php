<?php

namespace Model;

class ExtraEmploymentRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllExtraEmployments() {
        $result = $this->db->select("
        SELECT ts.id, ts.id_schedule, ts.week_day, ts.start, ts.end, e.id_person, e.description 
        FROM `out_of_school_employment` e 
        JOIN `time_span` ts ON ts.id = e.id_time_span
        ");
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'schedule' => $item['id_schedule'],
                'person' => $item['id_person'],
                'description' => $item['description'],
                'weekDay' => $item['week_day'],
                'start' => $item['start'],
                'end' => $item['end'],
            ];
        }, $result);
    }

    public function getOneExtraEmployment(int $id) {
        $result = $this->db->select("
        SELECT ts.id, ts.id_schedule, ts.week_day, ts.start, ts.end, e.id_person, e.description 
        FROM `out_of_school_employment` e 
        JOIN `time_span` ts ON ts.id = e.id_time_span
        WHERE ts.id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'schedule' => $result[0]['id_schedule'],
            'person' => $result[0]['id_person'],
            'description' => $result[0]['description'],
            'weekDay' => $result[0]['week_day'],
            'start' => $result[0]['start'],
            'end' => $result[0]['end'],
        ];
    }

    public function deleteExtraEmployment(int $id) {
        $this->db->executeStatement("
        DELETE FROM `time_span` WHERE `id` = $id
        ");
    }

    public function updateExtraEmployment(int $id, array $data): array {
        //todo transactions
        $set_values = [];
        if (isset($data['schedule'])) {
            $set_values[] = "`id_schedule` = {$data['schedule']}";
        }
        if (isset($data['weekDay'])) {
            $set_values[] = "`week_day` = {$data['weekDay']}";
        }
        if (isset($data['start'])) {
            $set_values[] = "`start` = {$data['start']}";
        }
        if (isset($data['end'])) {
            $set_values[] = "`end` = {$data['end']}";
        }
        $set_string = implode(", ", $set_values);
        if (strlen($set_string)) {
            $this->db->executeStatement("
            UPDATE `time_span`
            SET $set_string
            WHERE `id` = $id
            ");
        }
        $set_values = [];
        if (isset($data['person'])) {
            $set_values[] = "`id_person` = {$data['person']}";
        }
        if (isset($data['description'])) {
            $set_values[] = "`description` = \"{$data['description']}\"";
        }
        $set_string = implode(", ", $set_values);
        if (strlen($set_string)) {
            $this->db->executeStatement("
            UPDATE `out_of_school_employment`
            SET $set_string
            WHERE `id_time_span` = $id
            ");
        }
        return $this->getOneExtraEmployment($id);
    }

    public function createExtraEmployment(array $data): array {
        $this->db->executeStatement("
        INSERT INTO `time_span` (`id_schedule`, `week_day`, `start`, `end`) 
        VALUES ({$data['schedule']}, {$data['weekDay']}, {$data['start']}, {$data['end']})
        ");
        $new_id = $this->db->lastId();
        $this->db->executeStatement("
        INSERT INTO `out_of_school_employment` (`id_time_span`, `id_person`, `description`)
        VALUES ($new_id, {$data['person']}, \"{$data['description']}\")
        ");
        return $this->getOneExtraEmployment($new_id);
    }
}