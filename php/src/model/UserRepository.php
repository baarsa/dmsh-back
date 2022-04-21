<?php

namespace Model;

class UserRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllUsers() {
        $result = $this->db->select("
        SELECT id, name, login, password, roles, id_teacher FROM `user`
        ");
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'login' => $item['login'],
                'password' => $item['password'],
                'roles' => json_decode($item['roles']), // decode??
                'teacherId' => $item['id_teacher'],
            ];
        }, $result);
    }

    public function getOneUser(int $id) {
        $result = $this->db->select("
        SELECT id, name, login, password, roles, id_teacher FROM `user`
        WHERE id = $id
        ");
        if (count($result) < 1) {
            throw new \Exception("Объект не найден");
        }
        return [
            'id' => $result[0]['id'],
            'name' => $result[0]['name'],
            'login' => $result[0]['login'],
            'password' => $result[0]['password'],
            'roles' => json_decode($result[0]['roles']), // decode??
            'teacherId' => $result[0]['id_teacher'],
        ];
    }

    public function deleteUser(int $id) {
        $this->db->executeStatement("
        DELETE FROM `user` WHERE `id` = $id
        ");
    }

    public function updateUser(int $id, array $data) {
        $roles = json_encode($data['roles']);
        $id_teacher = $data['teacherId'] === null ? 'null' : $data['teacherId'];
        $this->db->executeStatement("
        UPDATE `user`
        SET `name` = \"{$data['name']}\", `login` = \"{$data['login']}\", `password` = \"{$data['password']}\", `roles` = \"$roles\", `id_teacher` = $id_teacher
        WHERE `id` = $id
        ");
    }

    public function createUser(array $data): int {
        $roles = json_encode($data['roles']);
        $this->db->executeStatement("
        INSERT INTO `user` (`name`, `login`, `password`, `roles`, `id_teacher`) 
        VALUES (\"{$data['name']}\", \"{$data['login']}\", \"{$data['password']}\", \"$roles\", {$data['teacherId']})
        ");
        return $this->db->lastId();
    }
}