<?php

final class ServiceArticles extends ServiceDB
{
    protected string $table = "articles";
    protected array $fields = ["article_id", "title", "slug", "description", "is_disabled", "created_at", "updated_at"];

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new ArticleRow($row);
        }

        return $list;
    }

    public function one(int $articleId): null|Error|ArticleRow
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

        return new ArticleRow($data);
    }

    public function createOrUpdate(ArticleRow $article): int|Error
    {
        $id = 0;
        $arData = [
            $article->title,
            $article->slug,
            $article->description,
            $article->is_disabled,
            $article->created_at,
            $article->updated_at,
        ];

        try {
            if ($article->article_id > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $article->article_id;
                $stmt->execute($arData);
                $id = $article->article_id;
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