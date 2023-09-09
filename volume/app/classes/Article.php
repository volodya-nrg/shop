<?php

class Article // Info занят php
{
    public int $articleId = 0;
    public string $title = "";
    public string $slug = "";
    public string $description = "";
    public bool $isDisabled = false;
    public string $updatedAt = "";
    public string $createdAt = "";

    public function parse(array $data): void
    {
        $this->articleId = $data["article_id"];
        $this->title = $data["title"];
        $this->slug = $data["slug"];
        $this->description = $data["description"];
        $this->isDisabled = $data["is_disabled"];
        $this->updatedAt = $data["updated_at"];
        $this->createdAt = $data["created_at"];
    }
}