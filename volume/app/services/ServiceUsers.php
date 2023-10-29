<?php

final class ServiceUsers extends ServiceDB
{
    protected string $table = "users";
    protected array $fields = ["user_id", "email", "pass", "email_hash", "avatar", "birthday_day", "birthday_mon", "role", "created_at", "updated_at"];

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new UserRow($row);
        }

        return $list;
    }

    public function one(int $userId): null|Error|UserRow
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[0]}=?");
            $stmt->execute([$userId]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new UserRow($data);
    }

    public function oneByEmail(string $email): null|Error|UserRow
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[1]}=?");
            $stmt->execute([$email]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new UserRow($data);
    }

    public function oneByEmailHash(string $hash): null|Error|UserRow
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[3]}=?");
            $stmt->execute([$hash]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new UserRow($data);
    }

    public function createOrUpdate(UserRow $user): int|Error
    {
        $id = 0;
        $emailHash = null;
        $avatar = null;
        $birthdayDay = null;
        $birthdayMon = null;
        $role = null;

        if (!empty($user->email_hash)) {
            $emailHash = $user->email_hash;
        }
        if (!empty($user->avatar)) {
            $avatar = $user->avatar;
        }
        if (!empty($user->birthday_day)) {
            $birthdayDay = $user->birthday_day;
        }
        if (!empty($user->birthday_mon)) {
            $birthdayMon = $user->birthday_mon;
        }
        if (!empty($user->role)) {
            $role = $user->role;
        }

        $arData = [
            $user->email,
            $user->pass,
            $emailHash,
            $avatar,
            $birthdayMon,
            $birthdayDay,
            $role,
            $user->created_at,
            $user->updated_at,
        ];

        try {
            if ($user->user_id > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $user->user_id;
                $stmt->execute($arData);
                $id = $user->user_id;
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} ({$this->fieldsAsString(true)}) 
                    VALUES ({$this->questionsAsString(true)})");

                $stmt->execute($arData);

                $tmp = $this->db->lastInsertId();
                if ($tmp) {
                    $id = (int)$this->db->lastInsertId();
                }
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $id;
    }

    public function delete(int $userId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[0]}=?")->
            execute([$userId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}