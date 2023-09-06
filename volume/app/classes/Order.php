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
}