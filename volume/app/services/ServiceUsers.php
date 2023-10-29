<?php

final class ServiceUsers extends ServiceDB
{
    protected string $table = "users";

    public function __construct(array $fields)
    {
        parent::__construct();
        $this->fields = $fields;
    }

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new UserTbl($row);
        }

        return $list;
    }

    public function one(int $userId): null|Error|UserTbl
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

        return new UserTbl($data);
    }

    public function oneByEmail(string $email): null|Error|UserTbl
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

        return new UserTbl($data);
    }

    public function oneByEmailHash(string $hash): null|Error|UserTbl
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

        return new UserTbl($data);
    }

    public function createOrUpdate(UserTbl $user): int|Error
    {
        $id = 0;
        $emailHash = null;
        $avatar = null;
        $birthdayDay = null;
        $birthdayMon = null;
        $role = null;

        if (!empty($user->emailHash)) {
            $emailHash = $user->emailHash;
        }
        if (!empty($user->avatar)) {
            $avatar = $user->avatar;
        }
        if (!empty($user->birthdayDay)) {
            $birthdayDay = $user->birthdayDay;
        }
        if (!empty($user->birthdayMon)) {
            $birthdayMon = $user->birthdayMon;
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
            $user->updatedAt,
            $user->createdAt
        ];

        try {
            if ($user->userId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $user->userId;
                $stmt->execute($arData);
                $id = $user->userId;
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