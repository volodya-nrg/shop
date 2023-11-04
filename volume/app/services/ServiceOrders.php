<?php

final class ServiceOrders extends ServiceDB
{
    protected string $table = "orders";
    protected array $fields = ["order_id", "user_id", "contact_phone", "contact_name", "comment", "place_delivery", "ip", "created_at", "updated_at"];

    public function all($limit = -1, $offset = -1): array|Error
    {
        $limitAndOffset = "";

        if ($limit > 0) {
            $limitAndOffset .= "LIMIT {$limit}";

            if ($offset > -1) {
                $limitAndOffset .= " OFFSET {$offset}";
            }
        }

        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->query("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                ORDER BY order_id DESC 
                {$limitAndOffset}");
            if ($stmt === false) {
                return new Error(ErrStmtIsFalse);
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

    public function one(int $orderId): null|Error|OrderRow
    {
        $arData = [$orderId];
        $fieldsString = implode(",", $this->fields);

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                WHERE order_id=?");
            if ($stmt === false) {
                return new Error(ErrStmtIsFalse);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                return new Error(ErrSqlQueryIsFalse);
            }

            $data = $stmt->fetch();
            if ($data === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new OrderRow($data);
    }

    public function create(OrderRow $order): int|Error
    {
        $id = 0;
        $userId = null;
        $contactName = null;
        $comment = null;
        $placeDelivery = null;

        if (!empty($order->user_id)) {
            $userId = $order->user_id;
        }
        if (!empty($order->contact_name)) {
            $contactName = $order->contact_name;
        }
        if (!empty($order->comment)) {
            $comment = $order->comment;
        }
        if (!empty($order->place_delivery)) {
            $placeDelivery = $order->place_delivery;
        }

        $arData = [
            $userId,
            $order->contact_phone,
            $contactName,
            $comment,
            $placeDelivery,
            $order->ip,
        ];

        try {
            $result = $this->db->prepare("
                    INSERT INTO {$this->table} (user_id, contact_phone, contact_name, comment, place_delivery, ip) 
                    VALUES (?,?,?,?,?,?)")->execute($arData);
            if ($result === false) {
                return new Error(ErrSqlQueryIsFalse);
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

    public function update(OrderRow $order): null|Error
    {
        $userId = null;
        $contactName = null;
        $comment = null;
        $placeDelivery = null;

        if (!empty($order->user_id)) {
            $userId = $order->user_id;
        }
        if (!empty($order->contact_name)) {
            $contactName = $order->contact_name;
        }
        if (!empty($order->comment)) {
            $comment = $order->comment;
        }
        if (!empty($order->place_delivery)) {
            $placeDelivery = $order->place_delivery;
        }

        $arData = [
            $userId,
            $order->contact_phone,
            $contactName,
            $comment,
            $placeDelivery,
            $order->ip,
            $order->order_id,
        ];

        try {
            $result = $this->db->prepare("
                    UPDATE {$this->table} 
                    SET user_id=?, contact_phone=?, contact_name=?, comment=?, place_delivery=?, ip=? 
                    WHERE order_id=?")->execute($arData);
            if ($result === false) {
                return new Error(ErrSqlQueryIsFalse);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return null;
    }

    public function delete(int $orderId): bool|Error
    {
        $arData = [$orderId];

        try {
            return $this->db->prepare("DELETE FROM {$this->table} WHERE order_id=?")->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}