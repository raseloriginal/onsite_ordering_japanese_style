<?php

namespace App\Models;

use Core\Model;

class NotificationModel extends Model {
    public function getUnread($role) {
        return $this->fetchAll("SELECT * FROM notifications WHERE user_role = ? AND is_read = FALSE ORDER BY created_at DESC", [$role]);
    }

    public function markAsRead($id) {
        return $this->query("UPDATE notifications SET is_read = TRUE WHERE id = ?", [$id]);
    }

    public function markAsReadForOrder($orderId, $role) {
        return $this->query("UPDATE notifications SET is_read = TRUE WHERE order_id = ? AND user_role = ?", [$orderId, $role]);
    }
}
