<?php

final class ServiceProps extends ServiceDB
{
    protected string $table = "props";

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
            $list[] = new PropTbl($row);
        }

        return $list;
    }

    public function one(int $propId): null|Error|PropTbl
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[0]}=?");
            $stmt->execute([$propId]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new PropTbl($data);
    }

    public function createOrUpdate(PropTbl $prop): int|Error
    {
        $id = 0;
        $arData = [
            $prop->propId,
            $prop->name,
        ];

        try {
            if ($prop->propId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
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
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[0]}=?")->
            execute([$propId]);
            // тут почистить все его значения
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}