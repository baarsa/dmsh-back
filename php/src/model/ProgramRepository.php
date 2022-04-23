<?php

namespace Model;

class ProgramRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllPrograms() {
        $result = $this->db->select("
        SELECT p.id, p.name, p.id_speciality_group, yp.speciality_half_hours, yp.year, ypcs.id_common_subject, ypcs.half_hours 
        FROM `program` p 
        JOIN `year_plan` yp ON yp.id_program = p.id
        JOIN `year_plan_common_subject` ypcs ON ypcs.id_year_plan = yp.id
        ");
        return array_values(array_reduce($result, function($acc, $item) {
            if (!array_key_exists($item['id'], $acc)) {
                $acc[$item['id']] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'specialityGroup' => $item['id_speciality_group'],
                    'yearPlans' => [],
                ];
            }
            if (!array_key_exists($item['year'] - 1, $acc[$item['id']]['yearPlans'])) {
                $acc[$item['id']]['yearPlans'][$item['year'] - 1] = [
                    'specialityHalfHours' => $item['speciality_half_hours'],
                    'commonSubjectsHalfHours' => []
                ];
            }
            $acc[$item['id']]['yearPlans'][$item['year'] - 1]['commonSubjectsHalfHours'][$item['id_common_subject']] = $item['half_hours'];
            return $acc;
        }, []));
    }

    public function getOneProgram(int $id) {
        $result = $this->db->select("
        SELECT p.id, p.name, p.id_speciality_group, yp.speciality_half_hours, yp.year, ypcs.id_common_subject, ypcs.half_hours 
        FROM `program` p 
        JOIN `year_plan` yp ON yp.id_program = p.id
        JOIN `year_plan_common_subject` ypcs ON ypcs.id_year_plan = yp.id
        WHERE p.id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'name' => $result[0]['name'],
            'specialityGroup' => $result[0]['id_speciality_group'],
            'yearPlans' => array_reduce($result, function($acc, $item) {
                if (!array_key_exists($item['year'] - 1, $acc)) {
                    $acc[$item['year'] - 1] = [
                        'specialityHalfHours' => $item['speciality_half_hours'],
                        'commonSubjectsHalfHours' => []
                    ];
                }
                $acc[$item['year'] - 1]['commonSubjectsHalfHours'][$item['id_common_subject']] = $item['half_hours'];
                return $acc;
            }, []),
        ];
    }

    public function deleteTeacher(int $id) {
        $this->db->executeStatement("
        DELETE FROM `program` WHERE `id` = $id
        ");
    }
    
    private function createYearPlans(int $program_id, array $year_plans) {
        $year_plans_items = array_map(function($yp, $i) use ($program_id) {
            $year_number = $i + 1;
            return "($program_id, $year_number, {$yp['specialityHalfHours']})";
        }, $year_plans, array_keys($year_plans));
        $year_plans_str = implode(", ", $year_plans_items);
        $this->db->executeStatement("
        INSERT INTO `year_plan` (`id_program`, `year`, `speciality_half_hours`)
        VALUES $year_plans_str
        ");
        $year_plans_ids = $this->db->getIds("SELECT `id` FROM `year_plan` WHERE `id_program` = $program_id ORDER BY `id` ASC");
        $items = array_map(function ($yp_id, $year_index) use ($year_plans) {
            $items = array_map(function ($common_subject) use ($yp_id, $year_plans, $year_index) {
                $half_hours = $year_plans[$year_index]['commonSubjectsHalfHours'][$common_subject];
                return "($yp_id, $common_subject, $half_hours)";
            }, array_keys($year_plans[$year_index]['commonSubjectsHalfHours']));
            return implode(", ", $items);
        }, $year_plans_ids, array_keys($year_plans_ids));
        $values_string = implode(", ",$items);
        $this->db->executeStatement("
        INSERT INTO `year_plan_common_subject` (`id_year_plan`, `id_common_subject`, `half_hours`) 
        VALUES $values_string
        ");
    }

    public function updateProgram(int $id, array $data): array {
        //todo transactions
        $this->db->executeStatement("
        UPDATE `program`
        SET `name` = \"{$data['name']}\", `id_speciality_group` = {$data['specialityGroup']}
        WHERE `id` = $id
        ");
        $this->db->executeStatement("DELETE FROM `year_plan` WHERE `id_program` = $id");
        $this->createYearPlans($id, $data['yearPlans']);
        return $this->getOneProgram($id);
    }

    public function createProgram(array $data): array {
        $this->db->executeStatement("
        INSERT INTO `program` (`name`, `id_speciality_group`) VALUES (\"{$data['name']}\", {$data['specialityGroup']})
        ");
        $new_program_id = $this->db->lastId();
        $this->createYearPlans($new_program_id, $data['yearPlans']);
        return $this->getOneProgram($new_program_id);
    }
}