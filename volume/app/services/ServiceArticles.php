<?php

final class ServiceArticles extends ServiceDB
{
    protected string $table = "articles";

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
            $list[] = new ArticleTbl($row);
        }

        return $list;
    }

    public function one(int $articleId): null|Error|ArticleTbl
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[0]}=?");
            $stmt->execute([$articleId]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new ArticleTbl($data);
    }

    public function createOrUpdate(ArticleTbl $article): int|Error
    {
        $id = 0;
        $arData = [
            $article->title,
            $article->slug,
            $article->description,
            $article->isDisabled,
            $article->updatedAt,
            $article->createdAt,
        ];

        try {
            if ($article->articleId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $article->articleId;
                $stmt->execute($arData);
                $id = $article->articleId;
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

    public function delete(int $articleId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[0]}=?")->
            execute([$articleId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}