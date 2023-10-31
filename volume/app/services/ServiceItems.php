<?php

final class ServiceItems extends ServiceDB
{
    protected string $table = "items";
    protected array $fields = ["item_id", "title", "slug", "cat_id", "description", "price", "is_disabled", "created_at", "updated_at"];

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
            $stmt = $this->db->query("SELECT {$fieldsString} FROM {$this->table} ORDER BY item_id DESC {$limitAndOffset}");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new ItemRow($row);
        }

        return $list;
    }

    public function one(int $itemId): null|Error|ItemRow
    {
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("SELECT {$fieldsString} FROM {$this->table} WHERE item_id=?");

            $stmt->execute([$itemId]);

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
        $id = 0;
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
                return new Error(ErrStmtIsFalse);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                return new Error(ErrSqlQueryIsFalse);
            }

            $tmp = $this->db->lastInsertId();
            if ($tmp) {
                $id = (int)$this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $id;
    }

    public function update(ItemRow $item): Error|null
    {
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
                UPDATE {$this->table} 
                SET title=?, slug=?, cat_id=?, description=?, price=?, is_disabled=? 
                WHERE item_id=?");
            if ($stmt === false) {
                return new Error(ErrStmtIsFalse);
            }

            $arData[] = $item->item_id;

            $result = $stmt->execute($arData);
            if ($result === false) {
                return new Error(ErrSqlQueryIsFalse);
            }

        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return null;
    }

    public function delete(int $itemId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE item_id=?")->
            execute([$itemId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}