<?php

class ServiceBase
{
    protected string $table = "";
    protected string $fields = "";

    protected \PDO $db;

    public function __construct()
    {
        $this->db = $GLOBALS["PDO"];
    }
}