<?php

class ServiceDB
{
    protected string $table = "";
    protected array $fields = [];
    public \PDO $db; // открыт для транзакций

    public function __construct()
    {
        $this->db = $GLOBALS["PDO"];
    }

    protected function fieldsAsString(bool $isWithoutFirstElement = false, string $separator = ","): string
    {
        $result = $this->fields;

        if ($isWithoutFirstElement) {
            array_shift($result);
        }

        return implode($separator, $result);
    }

    protected function questionsAsString(bool $isWithoutFirstElement = false): string
    {
        $questionsStr = str_repeat("?", count($this->fields));
        $questionsArray = explode("", $questionsStr);

        if ($isWithoutFirstElement) {
            array_shift($questionsArray);
        }

        return implode(",", $questionsArray);
    }
}