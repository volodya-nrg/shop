<?php

final class ServiceCats extends ServiceDB
{
    protected string $table = "cats";
    protected array $fields = ["cat_id", "name", "slug", "parent_id", "pos", "is_disabled"];

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
            $stmt = $this->db->query("SELECT {$fieldsString} FROM {$this->table} ORDER BY cat_id DESC {$limitAndOffset}");
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
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("SELECT {$fieldsString} FROM {$this->table} WHERE cat_id=?");
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

    public function create(CatRow $item): int|Error
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
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (name, slug, parent_id, pos, is_disabled) 
                VALUES (?,?,?,?,?)");
            if ($stmt === false) {
                return new Error(EnumErr::StmtIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                return new Error(EnumErr::SqlQueryIsFalse->value);
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

    public function update(CatRow $item): Error|null
    {
        $arData = [
            $item->name,
            $item->slug,
            $item->parent_id,
            $item->pos,
            (int)$item->is_disabled,
        ];

        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                    SET name=?, slug=?, parent_id=?, pos=?, is_disabled=? 
                    WHERE cat_id=?");
            if ($stmt === false) {
                return new Error(EnumErr::StmtIsFalse->value);
            }

            $arData[] = $item->cat_id;

            $result = $stmt->execute($arData);
            if ($result === false) {
                return new Error(EnumErr::SqlQueryIsFalse->value);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return null;
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