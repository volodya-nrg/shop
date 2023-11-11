<?php declare(strict_types=1);

final class ServiceRecovers
{
    private string $table = "recovers";
    private array $fields = ["hash", "user_id"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function one(string $hash): null|Error|RecoverRow
    {
        $arData = [$hash];
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE hash=?");
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

        return new RecoverRow($row);
    }

    public function create(RecoverRow $recover): null|Error
    {
        $arData = [
            $recover->hash,
            $recover->user_id,
        ];

        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (hash, user_id) 
                    VALUES (?,?)");
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

    public function deleteByUserId(int $userId): bool|Error
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