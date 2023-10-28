<?php

final class ServiceCats extends ServiceDB
{
    protected string $table = "cats";

    public function __construct(CatTbl $item)
    {
        parent::__construct();
        $this->fields = $item->fields;
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
            $list[] = new CatTbl($row);
        }

        return $list;
    }

    public function one(int $itemId): null|Error|CatTbl
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

        return new CatTbl($data);
    }

    public function createOrUpdate(CatTbl $item): int|Error
    {
        $id = 0;
        $arData = [
            $item->name,
            $item->slug,
            $item->parentId,
            $item->pos,
            $item->isDisabled,
        ];

        try {
            if ($item->catId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $item->catId;
                $stmt->execute($arData);
                $id = $item->catId;
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

    public function delete(int $catId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[0]}=?")->
            execute([$catId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}