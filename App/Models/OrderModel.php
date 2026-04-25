<?php

namespace App\Models;

use Core\Model;
use Exception;

class OrderModel extends Model {
    public function createOrder($data) {
        $this->db->beginTransaction();
        try {
            // 1. Insert Order
            $sql = "INSERT INTO orders (table_id, total_amount, payment_method, payment_status, order_status) 
                    VALUES (?, ?, ?, ?, ?)";
            
            // If online payment, mark as paid immediately. If hand, pending.
            $paymentStatus = ($data['payment_method'] === 'online') ? 'paid' : 'pending';
            // If online payment, it goes to chef immediately (pending). 
            // If hand, it stays pending until waiter approves.
            $orderStatus = ($data['payment_method'] === 'online') ? 'pending' : 'pending';

            $this->query($sql, [
                $data['table_id'],
                $data['total_amount'],
                $data['payment_method'],
                $paymentStatus,
                $orderStatus
            ]);
            $orderId = $this->lastInsertId();

            // 2. Insert Order Items
            $sqlItem = "INSERT INTO order_items (order_id, item_id, quantity, price, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
            foreach ($data['items'] as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $this->query($sqlItem, [
                    $orderId,
                    $item['id'],
                    $item['qty'],
                    $item['price'],
                    $subtotal
                ]);
            }

            // 3. Create Notification
            $notifSql = "INSERT INTO notifications (user_role, message, order_id) VALUES (?, ?, ?)";
            if ($data['payment_method'] === 'online') {
                // Notify Chef
                $this->query($notifSql, ['chef', "New order from Table " . $data['table_id'], $orderId]);
            } else {
                // Notify Waiter for payment
                $this->query($notifSql, ['waiter', "Payment collection needed at Table " . $data['table_id'], $orderId]);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getPendingForChef() {
        return $this->fetchAll("SELECT o.*, t.table_number 
                               FROM orders o 
                               JOIN tables t ON o.table_id = t.id 
                               WHERE o.order_status IN ('pending', 'cooking') 
                               AND o.payment_status = 'paid'
                               ORDER BY o.created_at ASC");
    }

    public function getOrderItems($orderId) {
        return $this->fetchAll("SELECT oi.*, i.name 
                               FROM order_items oi 
                               JOIN items i ON oi.item_id = i.id 
                               WHERE oi.order_id = ?", [$orderId]);
    }

    public function updateOrderStatus($orderId, $status) {
        return $this->query("UPDATE orders SET order_status = ? WHERE id = ?", [$status, $orderId]);
    }

    public function approvePayment($orderId) {
        // Mark as paid and notify chef
        $this->query("UPDATE orders SET payment_status = 'paid' WHERE id = ?", [$orderId]);
        $order = $this->fetch("SELECT table_id FROM orders WHERE id = ?", [$orderId]);
        $this->query("INSERT INTO notifications (user_role, message, order_id) VALUES (?, ?, ?)", 
                     ['chef', "New order from Table " . $order['table_id'], $orderId]);
    }
}
