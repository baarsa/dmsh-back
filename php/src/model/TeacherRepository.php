<?php
namespace Model;
class TeacherRepository
{

    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllTeachers() {
        $result = $this->db->select("
    SELECT p.id, p.name, t.can_assist, ts.id_subject FROM `teacher` t 
    JOIN `person` p ON p.id = t.id_person
    JOIN `teacher_subject` ts ON ts.id_teacher = p.id
    ");
        return array_values(array_reduce($result, function($acc, $item) {
            if (!array_key_exists($item['id'], $acc)) {
                $acc[$item['id']] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'canAssist' => $item['can_assist'] === '1',
                    'subjects' => [$item['id_subject']],
                ];
            } else {
                $acc[$item['id']]['subjects'][] = $item['id_subject'];
            }
            return $acc;
        }, []));
    }

    public function getOneTeacher(int $id) {
        $result = $this->db->select("
    SELECT p.id, p.name, t.can_assist, ts.id_subject FROM `teacher` t 
    JOIN `person` p ON p.id = t.id_person
    JOIN `teacher_subject` ts ON ts.id_teacher = p.id
    WHERE p.id = $id
    ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'name' => $result[0]['name'],
            'canAssist' => $result[0]['can_assist'] === '1',
            'subjects' => array_map(function ($item) {
                return $item['id_subject'];
            }, $result),
        ];
    }

    public function deleteTeacher(int $id) {
        $this->db->executeStatement("
        DELETE FROM `teacher` WHERE `id` = $id
        ");
    }

    public function updateTeacher(int $id, array $data) {
        //todo transactions
        $this->db->executeStatement("
        UPDATE `person`
        SET `name` = \"{$data['name']}\"
        WHERE `id` = $id
        ");
        $this->db->executeStatement("
        UPDATE `teacher`
        SET `can_assist` = {$data['canAssist']}
        WHERE `id_person` = $id
        ");
        $this->db->executeStatement("DELETE FROM `teacher_subject` WHERE id_teacher = $id");
        $values_string = implode(", ", array_map(function ($subject) use ($id) {
            return "($id, $subject)";
        }, $data['subjects']));
        $this->db->executeStatement("
        INSERT INTO `teacher_subject` (`id_teacher`, `id_subject`) 
        VALUES $values_string
        ");
    }

    public function createTeacher(array $data): int {
        $this->db->executeStatement("
        INSERT INTO `person` (`name`) VALUES (\"{$data['name']}\")
        ");
        $new_id = $this->db->lastId();
        $can_assist = $data['canAssist'] ? 1 : 0;
        $this->db->executeStatement("
        INSERT INTO `teacher` (`id_person`, `can_assist`)
        VALUES ($new_id, $can_assist)
        ");
        $values_string = implode(", ", array_map(function ($subject) use ($new_id) {
            return "($new_id, $subject)";
        }, $data['subjects']));
        $this->db->executeStatement("
        INSERT INTO `teacher_subject` (`id_teacher`, `id_subject`) 
        VALUES $values_string
        ");
        return $new_id;
    }
}