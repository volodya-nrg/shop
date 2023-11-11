<?php declare(strict_types=1);

final class ServiceCats
{
    private string $table = "cats";
    private array $fields = ["cat_id", "name", "slug", "parent_id", "pos", "is_disabled"];
    private $whereNameLike = "WHERE name LIKE ?";
    public \PDO $db;


    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Error|CatRow[]
     */
    public function all($limit = -1, $offset = -1, $filter = ""): array|Error
    {
        $limitAndOffset = "";
        $fieldsString = implode(",", $this->fields);
        $where = "";
        $arData = [];
        $list = [];

        if ($limit > 0) {
            $limitAndOffset .= "LIMIT {$limit}";

            if ($offset > -1) {
                $limitAndOffset .= " OFFSET {$offset}";
            }
        }
        if ($filter !== "") {
            $where = $this->whereNameLike;
            $arData[] = "%{$filter}%";
        }

        try {
            $stmt = $this->db->prepare("
                    SELECT {$fieldsString} 
                    FROM {$this->table}
                    {$where}
                    ORDER BY cat_id DESC {$limitAndOffset}");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
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
            $list[] = new CatRow($row);
        }

        return $list;
    }

    public function one(int $itemId): null|Error|CatRow
    {
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE cat_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute([$itemId]);
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

        return new CatRow($row);
    }

    public function create(CatRow $item): int|Error
    {
        $newId = 0;
        $arData = [
            $item->name,
            $item->slug,
            $item->parent_id,
            $item->pos,
            $item->is_disabled,
        ];

        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (name, slug, parent_id, pos, is_disabled) 
                VALUES (?,?,?,?,?)");
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

    public function update(CatRow $item): Error|null
    {
        $arData = [
            $item->name,
            $item->slug,
            $item->parent_id,
            $item->pos,
            $item->is_disabled,
            $item->cat_id,
        ];

        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET name=?, slug=?, parent_id=?, pos=?, is_disabled=? 
                WHERE cat_id=?");
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

    public function delete(int $catId): bool|Error
    {
        $arData = [$catId];

        try {
            $stmt = $this->db->prepare("
                DELETE 
                FROM {$this->table} 
                WHERE cat_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            return $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }

    public function total($filter = ""): int|Error
    {
        $where = "";
        $arData = [];

        if ($filter !== "") {
            $where = $this->whereNameLike;
            $arData[] = "%{$filter}%";
        }

        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*)
                FROM {$this->table}
                {$where}");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $total = $stmt->fetchColumn();
            if ($total === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $total;
    }
}