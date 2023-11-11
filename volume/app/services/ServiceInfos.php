<?php declare(strict_types=1);

final class ServiceInfos
{
    private string $table = "infos";
    private array $fields = ["info_id", "title", "slug", "description", "is_disabled", "created_at", "updated_at"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Error|InfoRow[]
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
                ORDER BY created_at DESC {$limitAndOffset}");
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
            $list[] = new InfoRow($row);
        }

        return $list;
    }

    public function one(int $infoId): null|Error|InfoRow
    {
        $arData = [$infoId];
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE info_id=?");
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

        return new InfoRow($row);
    }

    public function create(InfoRow $info): int|Error
    {
        $newId = 0;
        $arData = [
            $info->title,
            $info->slug,
            $info->description,
            $info->is_disabled,
        ];

        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (title, slug, description, is_disabled) 
                    VALUES (?,?,?,?)");
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

    public function update(InfoRow $info): null|Error
    {
        $arData = [
            $info->title,
            $info->slug,
            $info->description,
            $info->is_disabled,
            $info->info_id,
        ];

        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET title=?, slug=?, description=?, is_disabled=? 
                WHERE info_id=?");
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

    public function delete(int $infoId): bool|Error
    {
        $arData = [$infoId];

        try {
            $stmt = $this->db->prepare("
                DELETE 
                FROM {$this->table} 
                WHERE info_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            return $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}