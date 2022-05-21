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
                    'canAssist' => $item['can_assist'] === 1,
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
            'canAssist' => $result[0]['can_assist'] === 1,
            'subjects' => array_map(function ($item) {
                return $item['id_subject'];
            }, $result),
        ];
    }

    public function deleteTeacher(int $id) {
        $this->db->executeStatement("
        DELETE FROM `person` WHERE `id` = $id
        ");
    }

    public function createMultipleTeachers(array $data) {
        $values_str = implode(", ", array_map(function ($teacher) {
            return "(\"{$teacher['name']}\")";
        }, $data));
        $this->db->executeStatement("
        INSERT INTO `person` (`name`) VALUES $values_str
        ");
        $number_of_teachers = count($data);
        $persons_ids = array_reverse($this->db->getIds("SELECT `id` FROM `person` ORDER BY `id` DESC LIMIT $number_of_teachers"));
        $values_str = implode(", ", array_map(function ($id) {
            return "($id)";
        }, $persons_ids));
        $this->db->executeStatement("
        INSERT INTO `teacher` (`id_person`)
        VALUES $values_str
        ");
        $teacher_subject_rows = [];
        foreach ($data as $index => $item) {
            foreach ($item['subjects'] as $id_subject) {
                $teacher_subject_rows[] = [
                    "id_teacher" => $persons_ids[$index],
                    "id_subject" => $id_subject,
                ];
            }
        }
        $values_str = implode(", ", array_map(function ($row) {
            return "({$row["id_teacher"]}, {$row["id_subject"]})";
        }, $teacher_subject_rows));
        $this->db->executeStatement("
        INSERT INTO `teacher_subject` (`id_teacher`, `id_subject`)
        VALUES $values_str
        ");
    }

    public function updateTeacher(int $id, array $data): array {
        //todo transactions
        $this->db->executeStatement("
        UPDATE `person`
        SET `name` = \"{$data['name']}\"
        WHERE `id` = $id
        ");
        $can_assist = $data['canAssist'] ? 1 : 0;
        $this->db->executeStatement("
        UPDATE `teacher`
        SET `can_assist` = $can_assist
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
        return $this->getOneTeacher($id);
    }

    public function createTeacher(array $data): array {
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
        return $this->getOneTeacher($new_id);
    }
}