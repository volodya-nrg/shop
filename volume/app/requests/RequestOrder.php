<?php

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
            if (isset($post[FieldOrderId])) {
                $this->orderId = $post[FieldOrderId];
            }
            if (isset($post[FieldUserId])) {
                $this->userId = $post[FieldUserId];
            }
            if (isset($post[FieldContactPhone])) {
                $this->contactPhone = $post[FieldContactPhone];
            }
            if (isset($post[FieldContactName])) {
                $this->contactName = $post[FieldContactName];
            }
            if (isset($post[FieldComment])) {
                $this->comment = $post[FieldComment];
            }
            if (isset($post[FieldPlaceDelivery])) {
                $this->placeDelivery = $post[FieldPlaceDelivery];
            }
            if (isset($post[FieldIP])) {
                $this->ip = $post[FieldIP];
            }
            if (isset($post[FieldStatus])) {
                $this->status = $post[FieldStatus];
            }
        }
    }

    public function toArray(): array
    {
        return [
            FieldOrderId => $this->orderId,
            FieldUserId => $this->userId,
            FieldContactPhone => $this->contactPhone,
            FieldContactName => $this->contactName,
            FieldComment => $this->comment,
            FieldPlaceDelivery => $this->placeDelivery,
            FieldIP => $this->ip,
            FieldStatus => $this->status,
        ];
    }
}