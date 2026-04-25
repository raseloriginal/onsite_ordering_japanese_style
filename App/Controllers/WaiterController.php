<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\OrderModel;
use App\Models\NotificationModel;

class WaiterController extends Controller {
    public function index() {
        $orderModel = new OrderModel();
        $notifModel = new NotificationModel();
        
        // Orders waiting for payment
        $pendingPayments = $orderModel->fetchAll("SELECT o.*, t.table_number 
                                                 FROM orders o 
                                                 JOIN tables t ON o.table_id = t.id 
                                                 WHERE o.payment_status = 'pending' 
                                                 AND o.payment_method = 'hand'");
        
        // Notifications for waiter
        $notifications = $notifModel->getUnread('waiter');

        $this->view('waiter_dashboard', [
            'pendingPayments' => $pendingPayments,
            'notifications' => $notifications
        ]);
    }

    public function getData() {
        $orderModel = new OrderModel();
        $notifModel = new NotificationModel();
        
        $pendingPayments = $orderModel->fetchAll("SELECT o.*, t.table_number 
                                                 FROM orders o 
                                                 JOIN tables t ON o.table_id = t.id 
                                                 WHERE o.payment_status = 'pending' 
                                                 AND o.payment_method = 'hand'");
        
        $notifications = $notifModel->getUnread('waiter');

        $this->json([
            'pendingPayments' => $pendingPayments,
            'notifications' => $notifications
        ]);
    }

    public function approvePayment() {
        $data = json_decode(file_get_contents('php://input'), true);
        $orderModel = new OrderModel();
        $orderModel->approvePayment($data['order_id']);
        
        // Clear related notification
        $notifModel = new NotificationModel();
        $notifModel->markAsReadForOrder($data['order_id'], 'waiter');

        $this->json(['success' => true]);
    }

    public function markNotifRead() {
        $data = json_decode(file_get_contents('php://input'), true);
        $notifModel = new NotificationModel();
        $notifModel->markAsRead($data['id']);
        $this->json(['success' => true]);
    }
}
