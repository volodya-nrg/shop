<?php

final class ServiceRecovers extends ServiceDB
{
    protected string $table = "recovers";

    public function __construct(RecoverTbl $item)
    {
        parent::__construct();
        $this->fields = $item->fields;
    }

    public function one(string $hash): null|Error|RecoverTbl
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[0]}=?");
            $stmt->execute([$hash]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new RecoverTbl($data);
    }

    public function create(RecoverTbl $recover): null|Error
    {
        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} ({$this->fieldsAsString()}) 
                    VALUES ({$this->questionsAsString()})");
            $stmt->execute([
                $recover->hash,
                $recover->userId,
            ]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return null;
    }

    public function deleteByUserId(int $userId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[1]}=?")->
            execute([$userId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}