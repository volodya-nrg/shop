<?php

final class ServiceArticles extends ServiceDB
{
    protected string $table = "articles";
    protected array $fields = ["article_id", "title", "slug", "description", "is_disabled", "updated_at", "created_at"];

    public function all(): array|Error
    {
        $list = [];

        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY article_id DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($stmt->fetchAll() as $row) {
            $article = new Article();
            $article->parse($row);

            $list[] = $article;
        }

        return $list;
    }

    public function one(int $articleId): null|Error|Article
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE article_id=?");
            $stmt->execute([$articleId]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $article = new Article();
        $article->parse($data);

        return $article;
    }

    public function createOrUpdate(Article $article): int|Error
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
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE article_id=?");
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
            return $this->db->prepare("DELETE FROM {$this->table} WHERE article_id=?")->execute([$articleId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}