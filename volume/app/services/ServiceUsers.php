<?php

final class ServiceUsers extends ServiceDB
{
    protected string $table = "users";
    protected array $fields = ["user_id", "email", "pass", "email_hash", "avatar", "birthday_day", "birthday_mon", "role", "created_at", "updated_at"];

    public function all($limit = -1, $offset = -1): array|Error
    {
        $limitAndOffset = "";

        if ($limit > 0) {
            $limitAndOffset .= "LIMIT {$limit}";

            if ($offset > -1) {
                $limitAndOffset .= " OFFSET {$offset}";
            }
        }

        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->query("SELECT {$fieldsString} FROM {$this->table} ORDER BY {$this->fields[0]} DESC {$limitAndOffset}");
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
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("SELECT {$fieldsString} FROM {$this->table} WHERE email=?");
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
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("SELECT {$fieldsString} FROM {$this->table} WHERE email_hash=?");
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

    public function create(UserRow $user): int|Error
    {
        $userId = 0;
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
            $birthdayDay,
            $birthdayMon,
            $role,
        ];

        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (email, pass, email_hash, avatar, birthday_day, birthday_mon, role) 
                    VALUES (?,?,?,?,?,?,?)");

            $stmt->execute($arData);

            $tmp = $this->db->lastInsertId();
            if ($tmp) {
                $userId = (int)$this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $userId;
    }

    public function update(UserRow $user): null|Error
    {
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
            $birthdayDay,
            $birthdayMon,
            $role,
        ];

        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET email=?, pass=?, email_hash=?, avatar=?, birthday_day=?, birthday_mon=?, role=? 
                WHERE user_id=?");
            $arData[] = $user->user_id;
            $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return null;
    }

    public function delete(int $userId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE user_id=?")->
            execute([$userId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}