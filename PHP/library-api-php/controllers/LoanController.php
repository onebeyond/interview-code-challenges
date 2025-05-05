<?php

require_once __DIR__ . '/../data/data.php';
require_once __DIR__ . '/../models/BookStock.php';
require_once __DIR__ . '/../models/Fine.php';

class LoanController {
    // GET /loans
    public function index() {
        header('Content-Type: application/json');
        header('Status: 200');
        echo json_encode(bookList());
    }
    
    // POST /loans/return
    public function returnBook() {
        header('Content-Type: application/json');
        header('Status: 200');
        echo json_encode(returnBook($_POST['bookId']));
    }
}
