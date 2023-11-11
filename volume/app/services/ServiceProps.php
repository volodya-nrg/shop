<?php declare(strict_types=1);

final class ServiceProps
{
    private string $table = "props";
    private array $fields = ["prop_id", "name"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Error|PropRow[]
     */
    public function all(): array|Error
    {
        $fieldsString = implode(",", $this->fields);
        $list = [];

        try {
            $stmt = $this->db->query("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                ORDER BY prop_id DESC");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $rows = $stmt->fetchAll();
            if ($rows === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($rows as $row) {
            $list[] = new PropRow($row);
        }

        return $list;
    }

    public function one(int $propId): null|Error|PropRow
    {
        $arData = [$propId];
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE prop_id=?");
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

        return new PropRow($row);
    }

    public function create(PropRow $prop): int|Error
    {
        $newId = 0;
        $arData = [
            $prop->name,
        ];

        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (name) 
                    VALUES (?)");
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

    public function update(PropRow $prop): null|Error
    {
        $arData = [
            $prop->name,
        ];

        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET name=? 
                WHERE prop_id=?");
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

    public function delete(int $propId): bool|Error
    {
        $arData = [$propId];

        try {
            $stmt = $this->db->prepare("
                DELETE 
                FROM {$this->table} 
                WHERE prop_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            return $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}