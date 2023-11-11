<?php declare(strict_types=1);

final class OrderRow
{
    public int $order_id = 0;
    public ?int $user_id = null; // гости тоже могут создавать заказы
    public string $contact_phone = ""; // для связи обязательно нужен номер телефона
    public ?string $contact_name = null;
    public ?string $comment = null;
    public ?string $place_delivery = null;
    public string $ip = ""; // на всякий случай и ip
    public string $status = "";
    public string $created_at = "";
    public string $updated_at = "";
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}