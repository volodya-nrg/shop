<?php

final class ServiceOrders extends ServiceDB
{
    protected string $table = "orders";
    protected array $fields = ["order_id", "user_id", "contact_phone", "contact_name", "comment", "place_delivery", "ip", "updated_at", "created_at"];

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new OrderRow($row);
        }

        return $list;
    }

    public function one(int $orderId): null|Error|OrderRow
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE {$this->fields[0]}=?");
            $stmt->execute([$orderId]);
            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new OrderRow($data);
    }

    public function createOrUpdate(OrderRow $order): int|Error
    {
        $id = 0;
        $arData = [
            $order->user_id,
            $order->contact_phone,
            $order->contact_name,
            $order->comment,
            $order->place_delivery,
            $order->ip,
            $order->updated_at,
            $order->created_at
        ];

        try {
            if ($order->order_id > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $order->order_id;
                $stmt->execute($arData);
                $id = $order->order_id;
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} ({$this->fieldsAsString(true)}) 
                    VALUES ({$this->questionsAsString(true)})");

                $stmt->execute($arData);

                $tmp = $this->db->lastInsertId();
                if ($tmp) {
                    $id = (int)$this->db->lastInsertId();
                }
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $id;
    }

    public function delete(int $orderId): bool|Error
    {
        try {
            return $this->db->
            prepare("DELETE FROM {$this->table} WHERE {$this->fields[0]}=?")->
            execute([$orderId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}