<?php declare(strict_types=1);

final class ServiceOrders
{
    private string $table = "orders";
    private array $fields = ["order_id", "user_id", "contact_phone", "contact_name", "comment", "place_delivery", "ip", "status", "created_at", "updated_at"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Error|OrderRow[]
     */
    public function all($limit = -1, $offset = -1): array|Error
    {
        $limitAndOffset = "";
        $fieldsString = implode(",", $this->fields);
        $list = [];

        if ($limit > 0) {
            $limitAndOffset .= "LIMIT {$limit}";

            if ($offset > -1) {
                $limitAndOffset .= " OFFSET {$offset}";
            }
        }

        try {
            $stmt = $this->db->query("
                SELECT {$fieldsString} 
                FROM {$this->table} 
                ORDER BY order_id DESC {$limitAndOffset}");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $rows = $stmt->fetchAll();
            if ($rows === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        foreach ($rows as $row) {
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
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $row = $stmt->fetch();
            if ($row === false) {
                return null;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return new OrderRow($row);
    }

    public function create(OrderRow $order): int|Error
    {
        $newId = 0;
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
            $order->status,
        ];

        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (user_id, contact_phone, contact_name, comment, place_delivery, ip, status) 
                    VALUES (?,?,?,?,?,?,?)");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
            }

            $lastInsertId = $this->db->lastInsertId();
            if ($lastInsertId) {
                $newId = (int)$lastInsertId;
            }
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }

        return $newId;
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
            $order->status,
            $order->order_id,
        ];

        try {
            $stmt = $this->db->prepare("
                    UPDATE {$this->table} 
                    SET user_id=?, contact_phone=?, contact_name=?, comment=?, place_delivery=?, ip=?, status=?
                    WHERE order_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
                throw new \PDOException(EnumErr::SqlQueryIsFalse->value);
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
            $stmt = $this->db->prepare("
                DELETE 
                FROM {$this->table} 
                WHERE order_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::StmtIsFalse->value);
            }

            return $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}