<?php declare(strict_types=1);

final class ServiceItemsInOrder
{
    private string $table = "items_in_order";
    private array $fields = ["id", "item_id", "order_id", "amount", "price", "payload"];
    public \PDO $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Error|ItemsInOrderRow[]
     */
    public function allByOrderId(int $orderId): array|Error
    {
        $fieldsString = implode(",", $this->fields);
        $list = [];
        $arData = [$orderId];

        try {
            $stmt = $this->db->prepare("
                SELECT {$fieldsString} 
                FROM {$this->table}
                WHERE order_id=?
                ORDER BY id ASC");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            $result = $stmt->execute($arData);
            if ($result === false) {
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
            $list[] = new ItemsInOrderRow($row);
        }

        return $list;
    }

    public function create(ItemsInOrderRow $item): int|Error
    {
        $newId = 0;
        $arData = [
            $item->item_id,
            $item->order_id,
            $item->amount,
            $item->price,
            $item->payload,
        ];

        try {
            $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (item_id, order_id, amount, price, payload) 
                    VALUES (?,?,?,?,?)");
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

    public function deleteByOrderId(int $orderId): bool|Error
    {
        $arData = [$orderId];

        try {
            $stmt = $this->db->prepare("
                DELETE 
                FROM {$this->table} 
                WHERE order_id=?");
            if ($stmt === false) {
                throw new \PDOException(EnumErr::PrepareIsFalse->value);
            }

            return $stmt->execute($arData);
        } catch (\PDOException $e) {
            return new Error($e->getMessage());
        }
    }
}