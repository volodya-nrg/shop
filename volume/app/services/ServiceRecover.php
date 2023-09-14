<?php

final class ServiceRecover extends ServiceDB
{
    protected string $table = "recover";
    protected array $fields = ["hash", "user_id"];

    public function one(string $hash): null|Error|Recover
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE hash=?");
            $stmt->execute([$hash]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $user = new Recover();
        $user->parse($data);

        return $user;
    }

    public function create(Recover $recover): null|Error
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
            return $this->db->prepare("DELETE FROM {$this->table} WHERE user_id=?")->execute([$userId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}