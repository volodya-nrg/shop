<?php declare(strict_types=1);

final class ItemsInOrderRow extends XRow
{
    public int $id = 0;
    public int $item_id = 0;
    public int $order_id = 0;
    public int $amount = 0;
    public int $price = 0;
    public string $payload = "";
}