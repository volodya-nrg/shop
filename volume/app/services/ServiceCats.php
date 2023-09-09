<?php

final class ServiceCats extends ServiceDB
{
    protected string $table = "cats";
    protected array $fields = ["cat_id", "name", "slug", "parent_id", "pos", "is_disabled"];

    public function all(): array|Error
    {
        $list = [];

        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY cat_id DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($stmt->fetchAll() as $row) {
            $item = new Cat();
            $item->parse($row);

            $list[] = $item;
        }

        return $list;
    }

    public function one(int $itemId): Cat|Error
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE cat_id=?");
            $stmt->execute([$itemId]);
            $data = $stmt->fetch();
            if ($data === false) {
                throw new PDOException("not found cat_id");
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $item = new Cat();
        $item->parse($data);

        return $item;
    }

    public function createOrUpdate(Cat $item): int|Error
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
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE cat_id=?");
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
            return $this->db->prepare("DELETE FROM {$this->table} WHERE cat_id=?")->execute([$catId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}