<?php

final class ServiceOrders extends ServiceDB
{
    protected string $table = "orders";

    public function __construct(OrderTbl $item)
    {
        parent::__construct();
        $this->fields = $item->fields;
    }

    public function all(): array|Error
    {
        try {
            $stmt = $this->db->query("SELECT {$this->fieldsAsString()} FROM {$this->table} ORDER BY {$this->fields[0]} DESC");
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new OrderTbl($row);
        }

        return $list;
    }

    public function one(int $orderId): null|Error|OrderTbl
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

        return new OrderTbl($data);
    }

    public function createOrUpdate(OrderTbl $order): int|Error
    {
        $id = 0;
        $arData = [
            $order->userId,
            $order->contactPhone,
            $order->contactName,
            $order->comment,
            $order->placeDelivery,
            $order->ip,
            $order->updatedAt,
            $order->createdAt
        ];

        try {
            if ($order->orderId > 0) {
                $fields = $this->fieldsAsString(true, "=?,") . "=?";
                $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->fields[0]}=?");
                $arData[] = $order->orderId;
                $stmt->execute($arData);
                $id = $order->orderId;
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