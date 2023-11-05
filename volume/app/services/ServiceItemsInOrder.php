<?php

final class ServiceItemsInOrder extends ServiceDB
{
    protected string $table = "items_in_order";
    protected array $fields = ["id", "item_id", "order_id", "amount", "price", "payload"];

    public function allByOrderId(int $orderId): array|Error
    {
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->query("
                SELECT {$fieldsString} 
                FROM {$this->table}
                WHERE order_id={$orderId}
                ORDER BY id ASC");
            if ($stmt === false) {
                return new Error(EnumErr::StmtIsFalse->value);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new OrderRow($row);
        }

        return $list;
    }

    public function create(ItemsInOrderRow $item): int|Error
    {
        $id = 0;
        $arData = [
            $item->item_id,
            $item->order_id,
            $item->amount,
            $item->price,
            $item->payload,
        ];

        try {
            $result = $this->db->prepare("
                    INSERT INTO {$this->table} (item_id, order_id, amount, price, payload) 
                    VALUES (?,?,?,?,?)")->execute($arData);
            if ($result === false) {
                return new Error(EnumErr::SqlQueryIsFalse->value);
            }

            $tmp = $this->db->lastInsertId();
            if ($tmp) {
                $id = (int)$this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $id;
    }

    public function deleteByOrderId(int $orderId): bool|Error
    {
        $arData = [$orderId];

        try {
            return $this->db->prepare("DELETE FROM {$this->table} WHERE order_id=?")->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}