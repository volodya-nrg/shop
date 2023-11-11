<?php declare(strict_types=1);

final class ServiceUsers
{
    private string $table = "users";
    private array $fields = ["user_id", "email", "pass", "email_hash", "avatar", "birthday_day", "birthday_mon", "role", "created_at", "updated_at"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Error|UserRow[]
     */
    public function all($limit = -1, $offset = -1): array|Error
    {
        $limitAndOffset = "";
        $fieldsString = implode(",", $this->fields);
        $list = [];

        if ($limit > 0) {
            $limitAndOffset .= "LIMIT {$limit}";

            if ($offset > -1) {
                $limitAndOffset .= " OFFSET {$offset}";
            }
        }

        try {
            $stmt = $this->db->query("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                ORDER BY user_id DESC {$limitAndOffset}");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $rows = $stmt->fetchAll();
            if ($rows === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($rows as $row) {
            $list[] = new UserRow($row);
        }

        return $list;
    }

    public function one(int $userId): null|Error|UserRow
    {
        $arData = [$userId];
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE user_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $row = $stmt->fetch();
            if ($row === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new UserRow($row);
    }

    public function oneByEmail(string $email): null|Error|UserRow
    {
        $fieldsString = implode(",", $this->fields);
        $arData = [$email];

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE email=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $row = $stmt->fetch();
            if ($row === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new UserRow($row);
    }

    public function oneByEmailHash(string $hash): null|Error|UserRow
    {
        $fieldsString = implode(",", $this->fields);
        $arData = [$hash];

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE email_hash=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $row = $stmt->fetch();
            if ($row === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new UserRow($row);
    }

    public function create(UserRow $user): int|Error
    {
        $newId = 0;
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
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $lastInsertId = $this->db->lastInsertId();
            if ($lastInsertId) {
                $newId = (int)$lastInsertId;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $newId;
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
            $user->user_id,
        ];

        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET email=?, pass=?, email_hash=?, avatar=?, birthday_day=?, birthday_mon=?, role=? 
                WHERE user_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return null;
    }

    public function delete(int $userId): bool|Error
    {
        $arData = [$userId];

        try {
            $stmt = $this->db->prepare("
                DELETE 
                FROM {$this->table} 
                WHERE user_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            return $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}