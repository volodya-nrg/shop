<?php

final class ServiceItems extends ServiceDB
{
    protected string $table = "items";

    public function __construct(array $fields)
    {
        parent::__construct();
        $this->fields = $fields;
    }

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new ItemTbl($row);
        }

        return $list;
    }

    public function one(int $itemId): null|Error|ItemTbl
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

        return new ItemTbl($data);
    }

    public function createOrUpdate(ItemTbl $item): int|Error
    {
        $id = 0;
        $arData = [
            $item->title,
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
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
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
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[0]}=?")->
            execute([$itemId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}