<?php

final class ServiceOrders extends ServiceDB
{
    protected string $table = "orders";

    protected array $fields = ["order_id", "user_id", "contact_phone", "contact_name", "comment", "place_delivery", "ip", "updated_at", "created_at"];

    public function all(): array|Error
    {
        $list = [];

        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY `order_id` DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($stmt->fetchAll() as $row) {
            $item = new Order();
            $item->parse($row);

            $list[] = $item;
        }

        return $list;
    }

    public function one(int $orderId): Order|Error
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->fieldsAsString()} FROM {$this->table} WHERE `order_id`=?");
            $stmt->execute([$orderId]);
            $data = $stmt->fetch();
            if ($data === false) {
                throw new PDOException("not found order_id");
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $order = new Order();
        $order->parse($data);

        return $order;
    }

    public function createOrUpdate(Order $user): int|Error
    {
        $id = 0;
        $arData = [$user->userId, $user->contactPhone, $user->contactName, $user->comment, $user->placeDelivery, $user->ip, $user->updatedAt, $user->createdAt];

        try {
            if ($user->orderId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE order_id=?");
                $arData[] = $user->orderId;
                $stmt->execute($arData);
                $id = $user->orderId;
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
            return $this->db->prepare("DELETE FROM {$this->table} WHERE order_id=?")->execute([$orderId]);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}