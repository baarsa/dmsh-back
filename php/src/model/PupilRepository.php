<?php

namespace Model;

class PupilRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllPupils() {
        $result = $this->db->select("
        SELECT ps.id, ps.name, p.id_lesson_taker, p.id_program, p.id_special_subject FROM `pupil` p
        JOIN `person` ps ON ps.id = p.id_person
        ");
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'lessonTakerId' => $item['id_lesson_taker'],
                'name' => $item['name'],
                'program' => $item['id_program'],
                'specialSubject' => $item['id_special_subject'],
            ];
        }, $result);
    }

    public function getOnePupil(int $id) {
        $result = $this->db->select("
        SELECT ps.id, ps.name, p.id_lesson_taker, p.id_program, p.id_special_subject FROM `pupil` p
        JOIN `person` ps ON ps.id = p.id_person
        WHERE ps.id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'lessonTakerId' => $result[0]['id_lesson_taker'],
            'name' => $result[0]['name'],
            'program' => $result[0]['id_program'],
            'specialSubject' => $result[0]['id_special_subject'],
        ];
    }

    public function deletePupil(int $id) {
        $this->db->executeStatement("
        DELETE FROM `person` WHERE `id` = $id
        ");
    }

    public function updatePupil(int $id, array $data) {
        //todo transactions
        $this->db->executeStatement("
        UPDATE `person`
        SET `name` = \"{$data['name']}\"
        WHERE `id` = $id
        ");
        $this->db->executeStatement("
        UPDATE `pupil`
        SET `id_program` = {$data['program']}, `id_special_subject` = {$data['specialSubject']}
        WHERE `id_person` = $id
        ");
    }

    public function createPupil(array $data): int {
        $this->db->executeStatement("
        INSERT INTO `person` (`name`) VALUES (\"{$data['name']}\")
        ");
        $new_id = $this->db->lastId();
        $this->db->executeStatement("
        INSERT INTO `lesson_taker` () VALUES ()
        ");
        $new_lesson_taker_id = $this->db->lastId();
        $this->db->executeStatement("
        INSERT INTO `pupil` (`id_person`, `id_lesson_taker`, `id_program`, `id_special_subject`)
        VALUES ($new_id, $new_lesson_taker_id, {$data['program']}, {$data['specialSubject']})
        ");
        return $new_id;
    }
}