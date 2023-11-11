<?php declare(strict_types=1);

final class ServiceItems
{
    private string $table = "items";
    private array $fields = ["item_id", "title", "slug", "cat_id", "description", "price", "is_disabled", "created_at", "updated_at"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Error|ItemRow[]
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
            $list[] = new ItemRow($row);
        }

        return $list;
    }

    public function one(int $itemId): null|Error|ItemRow
    {
        $arData = [$itemId];
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE item_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new ItemRow($data);
    }

    public function create(ItemRow $item): int|Error
    {
        $newId = 0;
        $arData = [
            $item->title,
            $item->slug,
            $item->cat_id,
            $item->description,
            $item->price,
            (int)$item->is_disabled,
        ];

        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (title, slug, cat_id, description, price, is_disabled) 
                    VALUES (?,?,?,?,?,?)");
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

    public function update(ItemRow $item): null|Error
    {
        $arData = [
            $item->title,
            $item->slug,
            $item->cat_id,
            $item->description,
            $item->price,
            (int)$item->is_disabled,
            $item->item_id,
        ];

        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET title=?, slug=?, cat_id=?, description=?, price=?, is_disabled=? 
                WHERE item_id=?");
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

    public function delete(int $itemId): bool|Error
    {
        $arData = [$itemId];

        // TODO тут так же надо удалить и зависимости (данные о св-вах)

        try {
            $stmt = $this->db->prepare("
                DELETE 
                FROM {$this->table} 
                WHERE item_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            return $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}