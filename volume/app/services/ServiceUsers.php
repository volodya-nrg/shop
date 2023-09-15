<?php

final class ServiceUsers extends ServiceDB
{
    protected string $table = "users";
    protected array $fields = ["user_id", "email", "pass", "email_hash", "avatar", "birthday_day", "birthday_mon", "role", "updated_at", "created_at"];

    public function all(): array|Error
    {
        $list = [];

        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY user_id DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($stmt->fetchAll() as $row) {
            $user = new User();
            $user->parse($row);

            $list[] = $user;
        }

        return $list;
    }

    public function one(int $userId): null|Error|User
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE user_id=?");
            $stmt->execute([$userId]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $user = new User();
        $user->parse($data);

        return $user;
    }

    public function oneByEmail(string $email): Error|null|User
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE email=?");
            $stmt->execute([$email]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $user = new User();
        $user->parse($data);

        return $user;
    }
    public function oneByEmailHash(string $hash): Error|null|User
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE email_hash=?");
            $stmt->execute([$hash]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $user = new User();
        $user->parse($data);

        return $user;
    }

    public function createOrUpdate(User $user): int|Error
    {
        $id = 0;
        $arData = [
            $user->email,
            $user->pass,
            $user->emailHash,
            $user->avatar,
            $user->birthdayMon,
            $user->birthdayDay,
            $user->role,
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