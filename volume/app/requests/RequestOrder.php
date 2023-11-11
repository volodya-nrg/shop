<?php declare(strict_types=1);

final class RequestOrder
{
    public int $orderId = 0;
    public ?int $userId = 0;
    public string $contactPhone = "";
    public ?string $contactName = "";
    public ?string $comment = "";
    public ?string $placeDelivery = "";
    public string $ip = "";
    public string $status = "";

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (count($post)) {
            if (isset($post[EnumField::OrderId->value])) {
                $this->orderId = $post[EnumField::OrderId->value];
            }
            if (isset($post[EnumField::UserId->value])) {
                $this->userId = $post[EnumField::UserId->value];
            }
            if (isset($post[EnumField::ContactPhone->value])) {
                $this->contactPhone = $post[EnumField::ContactPhone->value];
            }
            if (isset($post[EnumField::ContactName->value])) {
                $this->contactName = $post[EnumField::ContactName->value];
            }
            if (isset($post[EnumField::Comment->value])) {
                $this->comment = $post[EnumField::Comment->value];
            }
            if (isset($post[EnumField::PlaceDelivery->value])) {
                $this->placeDelivery = $post[EnumField::PlaceDelivery->value];
            }
            if (isset($post[EnumField::IP->value])) {
                $this->ip = $post[EnumField::IP->value];
            }
            if (isset($post[EnumField::Status->value])) {
                $this->status = $post[EnumField::Status->value];
            }
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::OrderId->value => $this->orderId,
            EnumField::UserId->value => $this->userId,
            EnumField::ContactPhone->value => $this->contactPhone,
            EnumField::ContactName->value => $this->contactName,
            EnumField::Comment->value => $this->comment,
            EnumField::PlaceDelivery->value => $this->placeDelivery,
            EnumField::IP->value => $this->ip,
            EnumField::Status->value => $this->status,
        ];
    }
}