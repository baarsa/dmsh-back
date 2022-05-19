<?php

namespace Model;

class TeacherParser extends DataParser
{
    public function parse($array): array
    {
        $subjects = $this->db->select("SELECT * FROM `subject` s");
        $subjects_by_name = $this->getNameDictionary($subjects);
        $mapped_data = array_map(function ($item) use ($subjects_by_name) {
            $subject_names = explode(",", $item[1]);
            $subject_ids = array_map(function($name) use ($subjects_by_name) {
                return isset($subjects_by_name[$name]) ? $subjects_by_name[$name]["id"] : null;
            }, $subject_names);
            $correct_subject_ids = array_filter($subject_ids, function ($id) {
                return $id !== null;
            });
            if (count($correct_subject_ids) > 0) {
                return [
                    "name" => $item[0],
                    "subjects" => $correct_subject_ids,
                ];
            }
            return null;
        }, $array);
        $filtered_data = array_filter($mapped_data, function ($item) {
            return $item !== null;
        });
        return $filtered_data;
    }
}