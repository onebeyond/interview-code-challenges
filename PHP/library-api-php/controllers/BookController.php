<?php

require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../data/data.php';

class BookController {
    public function index() {
        // Returns a list of books
        header('Content-Type: application/json');
        echo json_encode($books);
    }
}
