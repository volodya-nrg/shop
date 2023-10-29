<?php

final class ServiceRecovers extends ServiceDB
{
    protected string $table = "recovers";
    protected array $fields = ["hash", "user_id"];

    public function one(string $hash): null|Error|RecoverRow
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

        return new RecoverRow($data);
    }

    public function create(RecoverRow $recover): null|Error
    {
        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} ({$this->fieldsAsString()}) 
                    VALUES ({$this->questionsAsString()})");
            $stmt->execute([
                $recover->hash,
                $recover->user_id,
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