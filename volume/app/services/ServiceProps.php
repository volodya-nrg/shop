<?php

final class ServiceProps extends ServiceDB
{
    protected string $table = "props";
    protected array $fields = ["prop_id", "name"];

    public function all(): array|Error
    {
        $list = [];

        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY prop_id DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($stmt->fetchAll() as $row) {
            $item = new Prop();
            $item->parse($row);

            $list[] = $item;
        }

        return $list;
    }

    public function one(int $propId): Prop|Error
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE prop_id=?");
            $stmt->execute([$propId]);
            $data = $stmt->fetch();
            if ($data === false) {
                throw new PDOException("not found prop_id");
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $prop = new Prop();
        $prop->parse($data);

        return $prop;
    }

    public function createOrUpdate(Prop $prop): int|Error
    {
        $id = 0;
        $arData = [
            $prop->propId,
            $prop->name,
        ];

        try {
            if ($prop->propId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE prop_id=?");
                $arData[] = $prop->propId;
                $stmt->execute($arData);
                $id = $prop->propId;
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

    public function delete(int $propId): bool|Error
    {
        try {
            return $this->db->prepare("DELETE FROM {$this->table} WHERE prop_id=?")->execute([$propId]);
            // тут почистить все его значения
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}