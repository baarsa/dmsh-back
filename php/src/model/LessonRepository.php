<?php

namespace Model;

class LessonRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllLessons() {
        $result = $this->db->select("
        SELECT ts.id, ts.id_schedule, ts.week_day, ts.start, ts.end, l.id_lesson_taker, l.id_teacher, l.id_subject 
        FROM `lesson` l 
        JOIN `time_span` ts ON ts.id = l.id_time_span
        ");
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'schedule' => $item['id_schedule'],
                'lessonTaker' => $item['id_lesson_taker'],
                'teacher' => $item['id_teacher'],
                'subject' => $item['id_subject'],
                'weekDay' => $item['week_day'],
                'start' => $item['start'],
                'end' => $item['end'],
            ];
        }, $result);
    }

    public function getOneLesson(int $id) {
        $result = $this->db->select("
        SELECT ts.id, ts.id_schedule, ts.week_day, ts.start, ts.end, l.id_lesson_taker, l.id_teacher, l.id_subject 
        FROM `lesson` l 
        JOIN `time_span` ts ON ts.id = l.id_time_span
        WHERE ts.id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'schedule' => $result[0]['id_schedule'],
            'lessonTaker' => $result[0]['id_lesson_taker'],
            'teacher' => $result[0]['id_teacher'],
            'subject' => $result[0]['id_subject'],
            'weekDay' => $result[0]['week_day'],
            'start' => $result[0]['start'],
            'end' => $result[0]['end'],
        ];
    }

    public function deleteLesson(int $id) {
        $this->db->executeStatement("
        DELETE FROM `time_span` WHERE `id` = $id
        ");
    }

    public function updateLesson(int $id, array $data): array {
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
        if (isset($data['lessonTaker'])) {
            $set_values[] = "`id_lesson_taker` = {$data['lessonTaker']}";
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
            UPDATE `lesson`
            SET $set_string
            WHERE `id_time_span` = $id
            ");
        }
        return $this->getOneLesson($id);
    }

    public function createLesson(array $data): array {
        $this->db->executeStatement("
        INSERT INTO `time_span` (`id_schedule`, `week_day`, `start`, `end`) 
        VALUES ({$data['schedule']}, {$data['weekDay']}, {$data['start']}, {$data['end']})
        ");
        $new_id = $this->db->lastId();
        $this->db->executeStatement("
        INSERT INTO `lesson` (`id_time_span`, `id_lesson_taker`, `id_teacher`, `id_subject`)
        VALUES ($new_id, {$data['lessonTaker']}, {$data['teacher']}, {$data['subject']})
        ");
        return $this->getOneLesson($new_id);
    }
}