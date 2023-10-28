<?php

final class OrderTbl implements InterfaceConstructData
{
    public array $fields = ["order_id", "user_id", "contact_phone", "contact_name", "comment", "place_delivery", "ip", "updated_at", "created_at"];
    public int $orderId = 0;
    public ?int $userId = null; // гости тоже могут создавать заказы
    public string $contactPhone = ""; // для связи обязательно нужен номер телефона
    public ?string $contactName = null;
    public ?string $comment = null;
    public ?string $placeDelivery = null;
    public string $ip = ""; // на всякий случай и ip
    public string $updatedAt = "";
    public string $createdAt = "";

    public function __construct(array $data)
    {
        $this->orderId = $data[$this->fields[0]];
        $this->userId = $data[$this->fields[1]];
        $this->contactPhone = $data[$this->fields[2]];
        $this->contactName = $data[$this->fields[3]];
        $this->comment = $data[$this->fields[4]];
        $this->placeDelivery = $data[$this->fields[5]];
        $this->ip = $data[$this->fields[6]];
        $this->updatedAt = $data[$this->fields[7]];
        $this->createdAt = $data[$this->fields[8]];
    }
}