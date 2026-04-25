<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\OrderModel;

class ChefController extends Controller {
    public function index() {
        $orderModel = new OrderModel();
        $orders = $orderModel->getPendingForChef();
        
        // Fetch items for each order
        foreach ($orders as &$order) {
            $order['items'] = $orderModel->getOrderItems($order['id']);
        }

        $this->view('chef_dashboard', ['orders' => $orders]);
    }

    public function getData() {
        $orderModel = new OrderModel();
        $orders = $orderModel->getPendingForChef();
        foreach ($orders as &$order) {
            $order['items'] = $orderModel->getOrderItems($order['id']);
        }
        $this->json(['orders' => $orders]);
    }

    public function updateStatus() {
        $data = json_decode(file_get_contents('php://input'), true);
        $orderModel = new OrderModel();
        $success = $orderModel->updateOrderStatus($data['order_id'], $data['status']);
        
        if ($data['status'] === 'ready') {
            // Notify Waiter
            $order = $orderModel->fetch("SELECT table_id FROM orders WHERE id = ?", [$data['order_id']]);
            $orderModel->query("INSERT INTO notifications (user_role, message, order_id) VALUES (?, ?, ?)", 
                               ['waiter', "Order for Table " . $order['table_id'] . " is ready to serve!", $data['order_id']]);
        }

        $this->json(['success' => $success]);
    }
}
