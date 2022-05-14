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
        SELECT ts.id, ts.id_schedule, ts.week_day, ts.start, ts.end, l.id_lesson_taker, l.id_teacher, l.id_subject,
               lat.id_teacher assistant, lat.time_start assistance_start, lat.time_end assistance_end
        FROM `lesson` l 
        JOIN `time_span` ts ON ts.id = l.id_time_span
        LEFT JOIN `lesson_additional_teacher` lat ON lat.id_lesson = ts.id
        ");
        return array_map(function ($item) {
            $lesson = [
                'id' => $item['id'],
                'schedule' => $item['id_schedule'],
                'lessonTaker' => $item['id_lesson_taker'],
                'teacher' => $item['id_teacher'],
                'subject' => $item['id_subject'],
                'weekDay' => $item['week_day'],
                'start' => $item['start'],
                'end' => $item['end'],
            ];
            if ($item['assistant'] !== null) {
                $lesson["assistance"] = [
                    'teacher' => $item['assistant'],
                    'start' => $item['assistance_start'],
                    'end' => $item['assistance_end'],
                ];
            }
            return $lesson;
        }, $result);
    }

    public function getOneLesson(int $id) {
        $result = $this->db->select("
        SELECT ts.id, ts.id_schedule, ts.week_day, ts.start, ts.end, l.id_lesson_taker, l.id_teacher, l.id_subject,
               lat.id_teacher assistant, lat.time_start assistance_start, lat.time_end assistance_end
        FROM `lesson` l 
        JOIN `time_span` ts ON ts.id = l.id_time_span
        LEFT JOIN `lesson_additional_teacher` lat ON lat.id_lesson = ts.id
        WHERE ts.id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        $lesson = [
            'id' => $result[0]['id'],
            'schedule' => $result[0]['id_schedule'],
            'lessonTaker' => $result[0]['id_lesson_taker'],
            'teacher' => $result[0]['id_teacher'],
            'subject' => $result[0]['id_subject'],
            'weekDay' => $result[0]['week_day'],
            'start' => $result[0]['start'],
            'end' => $result[0]['end'],
        ];
        if ($result[0]['assistant'] !== null) {
            $lesson["assistance"] = [
                'teacher' => $result[0]['assistant'],
                'start' => $result[0]['assistance_start'],
                'end' => $result[0]['assistance_end'],
            ];
        }
        return $lesson;
    }

    public function deleteLesson(int $id) {
        $this->db->executeStatement("
        DELETE FROM `time_span` WHERE `id` = $id
        ");
    }

    public function deleteAssistance($id_lesson) {
        $this->db->executeStatement("
            DELETE FROM `lesson_additional_teacher` WHERE `id_lesson` = $id_lesson
            ");
        return $this->getOneLesson($id_lesson);
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
        if (isset($data['assistance'])) {
            $this->db->executeStatement("
            DELETE FROM `lesson_additional_teacher` WHERE `id_lesson` = $id
            ");
            $this->db->executeStatement("
            INSERT INTO `lesson_additional_teacher` (`id_lesson`, `id_teacher`, `time_start`, `time_end`) 
            VALUES ($id, {$data['assistance']['teacher']}, {$data['assistance']['start']}, {$data['assistance']['end']})
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