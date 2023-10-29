<?php

// Info занят php
final class ArticleRow implements InterfaceConstructData
{
    public int $article_id = 0;
    public string $title = "";
    public string $slug = "";
    public string $description = "";
    public bool $is_disabled = false;
    public string $created_at = "";
    public string $updated_at = "";

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}