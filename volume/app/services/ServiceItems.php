<?php

final class ServiceItems extends ServiceDB
{
    protected string $table = "items";
    protected array $fields = ["item_id", "name", "slug", "cat_id", "description", "price", "is_disabled", "updated_at", "created_at"];

    public function all(): array|Error
    {
        $list = [];

        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY item_id DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($stmt->fetchAll() as $row) {
            $item = new Item();
            $item->parse($row);

            $list[] = $item;
        }

        return $list;
    }

    public function one(int $itemId): null|Error|Item
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE item_id=?");
            $stmt->execute([$itemId]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $item = new Item();
        $item->parse($data);

        return $item;
    }

    public function createOrUpdate(Item $item): int|Error
    {
        $id = 0;
        $arData = [
            $item->itemId,
            $item->name,
            $item->slug,
            $item->catId,
            $item->description,
            $item->price,
            $item->isDisabled,
            $item->updatedAt,
            $item->createdAt
        ];

        try {
            if ($item->itemId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE item_id=?");
                $arData[] = $item->itemId;
                $stmt->execute($arData);
                $id = $item->itemId;
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
            return $this->db->prepare("DELETE FROM {$this->table} WHERE item_id=?")->execute([$itemId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}