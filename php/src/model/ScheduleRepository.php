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
        WHERE s.id = $id
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

    public function updateSchedule(int $id, array $data): array {
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
        return $this->getOneSchedule($id);
    }

    public function createSchedule(array $data): array {
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
        return $this->getOneSchedule($new_id);
    }

    private function getNextYearPupils($pupils) {
        $pupils_ids_string = implode(", ", array_map(function ($item) {
            return "{$item['id_pupil']}";
        }, $pupils));;
        $data = $this->db->select("
        SELECT p.id_person id_pupil, COUNT(yp.year) program_years 
        FROM `year_plan` yp
        JOIN `pupil` p ON yp.id_program = p.id_program
        WHERE p.id_person IN ($pupils_ids_string)
        GROUP BY p.id_person
        ");
        $years_by_id_pupil = array_reduce($data, function($acc, $item) {
            $acc[$item['id_pupil']] = $item['program_years'];
            return $acc;
        }, []);
        $mapped_pupils = array_map(function($item) {
            return [
                'id_pupil' => $item['id_pupil'],
                'year' => $item['year'] + 1,
            ];
        }, $pupils);
        return array_filter($mapped_pupils, function ($item) use ($years_by_id_pupil) {
            return $item['year'] <= $years_by_id_pupil[$item['id_pupil']];
        });
    }

    public function copySchedule(int $id_schedule, string $name, bool $next_year): array
    {
        $original_schedule_pupils = $this->db->select("SELECT `id_pupil`, `year` FROM `pupil_schedule` WHERE `id_schedule` = $id_schedule");
        $this->db->executeStatement("
        INSERT INTO `schedule` (`name`) VALUES (\"$name\")
        ");
        $new_id = $this->db->lastId();
        $new_schedule_pupils = $next_year
            ? $this->getNextYearPupils($original_schedule_pupils)
            : $original_schedule_pupils;
        $values_string = implode(", ", array_map(function ($item) use ($new_id) {
            return "($new_id, {$item['id_pupil']}, {$item['year']})";
        }, $new_schedule_pupils));
        $this->db->executeStatement("
        INSERT INTO `pupil_schedule` (`id_schedule`, `id_pupil`, `year`) 
        VALUES $values_string
        ");
        return $this->getOneSchedule($new_id);
    }
}