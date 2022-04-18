<?php
namespace Model;
class Teacher
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
                    'can_assist' => $item['can_assist'] === '1',
                    'subjects' => [$item['id_subject']],
                ];
            } else {
                $acc[$item['id']]['subjects'][] = $item['id_subject'];
            }
            return $acc;
        }, []));
    }

    public function getOneTeacher(int $id) {
        return $this->db->select('');
    }

    public function deleteTeacher(int $id) {
        return $this->db->select('');
    }

    public function updateTeacher(int $id, array $data) {
        return $this->db->select('');
    }

    public function createTeacher(array $data): int {
        $this->db->select('');
        return 0;
    }
}