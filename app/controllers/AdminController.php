<?php

namespace App\Controllers;

use Core\Controller;
use Core\Model;

class AdminController extends Controller {
    public function index() {
        $model = new Model();
        
        // Total Revenue
        $revenue = $model->fetch("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'")['total'] ?? 0;
        
        // Total Orders
        $orderCount = $model->fetch("SELECT COUNT(*) as total FROM orders")['total'];
        
        // Total Expenses
        $expenses = $model->fetch("SELECT SUM(amount) as total FROM expenses")['total'] ?? 0;
        
        // Daily Income for Chart (Last 7 days)
        $dailyIncome = $model->fetchAll("SELECT DATE(created_at) as date, SUM(total_amount) as total 
                                        FROM orders 
                                        WHERE payment_status = 'paid' 
                                        AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                        GROUP BY DATE(created_at)
                                        ORDER BY date ASC");

        $this->view('admin_dashboard', [
            'revenue' => $revenue,
            'orderCount' => $orderCount,
            'expenses' => $expenses,
            'dailyIncome' => $dailyIncome
        ]);
    }

    public function items() {
        $model = new \App\Models\ItemModel();
        $items = $model->getAvailable();
        $categoryModel = new \App\Models\CategoryModel();
        $categories = $categoryModel->getAll();
        $this->view('admin_items', ['items' => $items, 'categories' => $categories]);
    }

    public function tables() {
        $model = new \App\Models\TableModel();
        $tables = $model->getAll();
        $this->view('admin_tables', ['tables' => $tables]);
    }

    public function expenses() {
        $model = new \Core\Model();
        $expenses = $model->fetchAll("SELECT * FROM expenses ORDER BY expense_date DESC");
        $this->view('admin_expenses', ['expenses' => $expenses]);
    }

    public function income() {
        $model = new \Core\Model();
        $income = $model->fetchAll("SELECT o.*, t.table_number 
                                   FROM orders o 
                                   JOIN tables t ON o.table_id = t.id 
                                   WHERE o.payment_status = 'paid' 
                                   ORDER BY o.created_at DESC");
        $this->view('admin_income', ['income' => $income]);
    }

    public function saveItem() {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new \Core\Model();
        
        if (isset($data['id']) && !empty($data['id'])) {
            // Update
            $sql = "UPDATE items SET name = ?, category_id = ?, price = ?, description = ?, image = ?, status = ? WHERE id = ?";
            $success = $model->query($sql, [
                $data['name'], $data['category_id'], $data['price'], $data['description'], 
                $data['image'], $data['status'], $data['id']
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO items (name, category_id, price, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
            $success = $model->query($sql, [
                $data['name'], $data['category_id'], $data['price'], $data['description'], 
                $data['image'], $data['status']
            ]);
        }
        
        $this->json(['success' => (bool)$success]);
    }

    public function deleteItem() {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new \Core\Model();
        $success = $model->query("DELETE FROM items WHERE id = ?", [$data['id']]);
        $this->json(['success' => (bool)$success]);
    }

    public function saveTable() {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new \Core\Model();
        if (isset($data['id']) && !empty($data['id'])) {
            $success = $model->query("UPDATE tables SET table_number = ?, status = ? WHERE id = ?", [$data['table_number'], $data['status'], $data['id']]);
        } else {
            $success = $model->query("INSERT INTO tables (table_number, status) VALUES (?, ?)", [$data['table_number'], $data['status']]);
        }
        $this->json(['success' => (bool)$success]);
    }

    public function deleteTable() {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new \Core\Model();
        $success = $model->query("DELETE FROM tables WHERE id = ?", [$data['id']]);
        $this->json(['success' => (bool)$success]);
    }

    public function saveExpense() {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new \Core\Model();
        $success = $model->query("INSERT INTO expenses (title, category, amount, expense_date) VALUES (?, ?, ?, ?)", [
            $data['title'], $data['category'], $data['amount'], $data['expense_date']
        ]);
        $this->json(['success' => (bool)$success]);
    }

    public function deleteExpense() {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new \Core\Model();
        $success = $model->query("DELETE FROM expenses WHERE id = ?", [$data['id']]);
        $this->json(['success' => (bool)$success]);
    }
}
