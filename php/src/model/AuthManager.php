<?php

namespace Model;

class AuthManager
{
    static string $SESSION_ID_KEY = "USER_ID";
    private Database $db;

    public function auth() {
        if (array_key_exists(self::$SESSION_ID_KEY, $_SESSION)) {
            $id = $_SESSION[self::$SESSION_ID_KEY];
            $result = $this->db->select("SELECT * FROM `user` WHERE `id` = $id");
            if (count($result) < 1) {
                throw new \Exception("Объект не найден");
            }
            return [
                'name' => $result[0]['name'],
                'roles' => json_decode($result[0]['roles']),
                'teacherId' => $result[0]['id_teacher'],
            ];
        }
        return null;
    }

    public function login(string $login, string $password) {
        $result = $this->db->select("SELECT * FROM `user` WHERE `login` = \"$login\" AND `password` = \"$password\"");
        if (count($result) < 1) {
            return null;
        }
        $_SESSION[self::$SESSION_ID_KEY] = $result[0]['id'];
        return [
            'name' => $result[0]['name'],
            'roles' => json_decode($result[0]['roles']),
            'teacherId' => $result[0]['id_teacher'],
        ];
    }

    public function logout() {
        unset($_SESSION[self::$SESSION_ID_KEY]);
    }

    public function __construct(Database $db) {
        $this->db = $db;
    }
}