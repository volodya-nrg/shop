<?php

// Info занят php
final class ArticleTbl implements InterfaceConstructData
{
    public array $fields = ["article_id", "title", "slug", "description", "is_disabled", "updated_at", "created_at"];
    public int $articleId = 0;
    public string $title = "";
    public string $slug = "";
    public string $description = "";
    public bool $isDisabled = false;
    public string $updatedAt = "";
    public string $createdAt = "";

    public function __construct(array $data = [])
    {
        if (count($data)) {
            $this->articleId = $data[$this->fields[0]];
            $this->title = $data[$this->fields[1]];
            $this->slug = $data[$this->fields[2]];
            $this->description = $data[$this->fields[3]];
            $this->isDisabled = $data[$this->fields[4]];
            $this->updatedAt = $data[$this->fields[5]];
            $this->createdAt = $data[$this->fields[6]];
        }
    }
}