<?php

final class ServiceUsers extends ServiceDB
{
    protected string $table = "users";
    protected array $fields = ["user_id", "email", "pass", "hash_for_check_email", "avatar", "birthday_day", "birthday_mon", "updated_at", "created_at"];

    public function all(): array|Error
    {
        $list = [];

        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY user_id DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($stmt->fetchAll() as $row) {
            $user = new Info();
            $user->parse($row);

            $list[] = $user;
        }

        return $list;
    }

    public function one(int $userId): Info|Error
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE user_id=?");
            $stmt->execute([$userId]);
            $data = $stmt->fetch();
            if ($data === false) {
                throw new PDOException("not found user_id");
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $user = new Info();
        $user->parse($data);

        return $user;
    }

    public function createOrUpdate(Info $user): int|Error
    {
        $id = 0;
        $arData = [
            $user->email,
            $user->pass,
            $user->hashForCheckEmail,
            $user->avatar,
            $user->birthdayMon,
            $user->birthdayDay,
            $user->updatedAt,
            $user->createdAt
        ];

        try {
            if ($user->userId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE user_id=?");
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
            return $this->db->prepare("DELETE FROM {$this->table} WHERE user_id=?")->execute([$userId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}