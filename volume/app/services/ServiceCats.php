<?php

final class ServiceCats extends ServiceDB
{
    protected string $table = "cats";
    protected array $fields = ["cat_id", "name", "slug", "parent_id", "pos", "is_disabled"];

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new CatRow($row);
        }

        return $list;
    }

    public function one(int $itemId): null|Error|CatRow
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

        return new CatRow($data);
    }

    public function createOrUpdate(CatRow $item): int|Error
    {
        $id = 0;
        $arData = [
            $item->name,
            $item->slug,
            $item->parent_id,
            $item->pos,
            (int)$item->is_disabled,
        ];

        try {
            if ($item->cat_id > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $item->cat_id;
                $stmt->execute($arData);
                $id = $item->cat_id;
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$this->fieldsAsString(true)}) VALUES ({$this->questionsAsString(true)})");
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