<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\TableModel;

class HomeController extends Controller {
    public function index() {
        $tableModel = new TableModel();
        $tables = $tableModel->getAll();
        $this->view('home', ['tables' => $tables]);
    }
}
