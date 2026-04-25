<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\TableModel;
use App\Models\ItemModel;
use App\Models\CategoryModel;

class OrderController extends Controller {
    public function table($number) {
        $tableModel = new TableModel();
        $itemModel = new ItemModel();
        $categoryModel = new CategoryModel();

        $table = $tableModel->getByTableNumber($number);
        if (!$table) {
            $this->redirect('/');
        }

        $categories = $categoryModel->getAll();
        $items = $itemModel->getAvailable();

        $this->view('order_menu', [
            'table' => $table,
            'categories' => $categories,
            'items' => $items
        ]);
    }

    public function placeOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->json(['success' => false, 'message' => 'Invalid data']);
        }

        try {
            $orderModel = new \App\Models\OrderModel();
            $orderId = $orderModel->createOrder($data);
            $this->json(['success' => true, 'order_id' => $orderId]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
