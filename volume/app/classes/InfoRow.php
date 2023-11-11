<?php declare(strict_types=1);

final class InfoRow extends XRow
{
    public int $info_id = 0;
    public string $title = "";
    public string $slug = "";
    public string $description = "";
    public int $is_disabled = 0;
    public string $created_at = "";
    public string $updated_at = "";
}