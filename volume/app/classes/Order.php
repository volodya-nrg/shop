<?php

class Order
{
    public int $orderId = 0;
    public ?int $userId = 0; // гости тоже могут создавать заказы
    public string $contactPhone = ""; // для связи обязательно нужен номер телефона
    public ?string $contactName = "";
    public ?string $comment = "";
    public ?string $placeDelivery = "";
    public string $ip = ""; // на всякий случай и ip
    public string $updatedAt = "";
    public string $createdAt = "";

    public function parse(array $data): void
    {
        $this->orderId = $data["order_id"];
        $this->userId = $data["user_id"];
        $this->contactPhone = $data["contact_phone"];
        $this->contactName = $data["contact_name"];
        $this->comment = $data["comment"];
        $this->placeDelivery = $data["place_delivery"];
        $this->ip = $data["ip"];
        $this->updatedAt = $data["updated_at"];
        $this->createdAt = $data["created_at"];
    }
}