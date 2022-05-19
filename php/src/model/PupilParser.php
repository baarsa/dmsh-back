<?php

namespace Model;

class PupilParser extends DataParser
{
    public function parse($array): array
    {
        $programs = $this->db->select("SELECT * FROM `program`");
        $subjects = $this->db->select("
            SELECT * FROM `subject` s 
            JOIN `special_subject` ss ON ss.id_subject = s.id");
        $programs_by_name = $this->getNameDictionary($programs);
        $subjects_by_name = $this->getNameDictionary($subjects);
        $mapped_data = array_map(function ($item) use ($programs_by_name, $subjects_by_name) {
            $program_name = $item[1];
            $subject_name = $item[2];
            if (isset($programs_by_name[$program_name]) && isset($subjects_by_name[$subject_name])) {
                return [
                    "name" => $item[0],
                    "program" => $programs_by_name[$program_name]["id"],
                    "specialSubject" => $subjects_by_name[$subject_name]["id"],
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