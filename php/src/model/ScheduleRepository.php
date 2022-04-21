<?php

namespace Model;

class ScheduleRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllSchedules() {
        $result = $this->db->select("
        SELECT s.id, s.name, ps.id_pupil, ps.year 
        FROM `schedule` s
        JOIN `pupil_schedule` ps ON ps.id_schedule = s.id
        ");
        return array_values(array_reduce($result, function($acc, $item) {
            if (!array_key_exists($item['id'], $acc)) {
                $acc[$item['id']] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'pupilsYears' => [],
                ];
            }
            $acc[$item['id']]['pupilsYears'][$item['id_pupil']] = $item['year'];
            return $acc;
        }, []));
    }

    public function getOneSchedule(int $id) {
        $result = $this->db->select("
        SELECT s.id, s.name, ps.id_pupil, ps.year 
        FROM `schedule` s
        JOIN `pupil_schedule` ps ON ps.id_schedule = s.id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'name' => $result[0]['name'],
            'pupilsYears' => array_reduce($result, function($acc, $item) {
                $acc[$item['id_pupil']] = $item['year'];
                return $acc;
            }, []),
        ];
    }

    public function deleteSchedule(int $id) {
        $this->db->executeStatement("
        DELETE FROM `schedule` WHERE `id` = $id
        ");
    }

    public function updateSchedule(int $id, array $data) {
        //todo transactions
        $this->db->executeStatement("
        UPDATE `schedule`
        SET `name` = \"{$data['name']}\"
        WHERE `id` = $id
        ");
        $this->db->executeStatement("DELETE FROM `pupil_schedule` WHERE id_schedule = $id");
        $values_string = implode(", ", array_map(function ($year, $id_pupil) use ($id) {
            return "($id, $id_pupil, $year)";
        }, $data['pupilsYears'], array_keys($data['pupilsYears'])));
        $this->db->executeStatement("
        INSERT INTO `pupil_schedule` (`id_schedule`, `id_pupil`, `year`) 
        VALUES $values_string
        ");
    }

    public function createSchedule(array $data): int {
        $this->db->executeStatement("
        INSERT INTO `schedule` (`name`) VALUES (\"{$data['name']}\")
        ");
        $new_id = $this->db->lastId();
        $values_string = implode(", ", array_map(function ($year, $id_pupil) use ($new_id) {
            return "($new_id, $id_pupil, $year)";
        }, $data['pupilsYears'], array_keys($data['pupilsYears'])));
        $this->db->executeStatement("
        INSERT INTO `pupil_schedule` (`id_schedule`, `id_pupil`, `year`) 
        VALUES $values_string
        ");
        return $new_id;
    }
}