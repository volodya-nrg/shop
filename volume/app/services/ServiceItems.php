<?php

final class ServiceItems extends ServiceDB
{
    protected string $table = "items";
    protected array $fields = ["item_id", "title", "slug", "cat_id", "description", "price", "is_disabled", "updated_at", "created_at"];

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
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
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[0]}=?");
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

    public function createOrUpdate(ItemRow $item): int|Error
    {
        $id = 0;
        $arData = [
            $item->title,
            $item->slug,
            $item->cat_id,
            $item->description,
            $item->price,
            $item->is_disabled,
            $item->updated_at,
            $item->created_at
        ];

        try {
            if ($item->item_id > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $item->item_id;
                $stmt->execute($arData);
                $id = $item->item_id;
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

    public function delete(int $itemId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[0]}=?")->
            execute([$itemId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}