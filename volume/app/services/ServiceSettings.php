<?php declare(strict_types=1);

final class ServiceSettings
{
    private string $table = "settings";
    private array $fields = ["setting_id", "value"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }
}