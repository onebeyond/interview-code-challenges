<?php

require 'data/data.php';
require 'controllers/BookController.php';
require 'controllers/LoanController.php';
require 'controllers/ReservationController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/books' && $method === 'GET') {
    $controller = new BookController();
    $controller->index();
} elseif ($uri === '/loans' && $method === 'GET') {
    $controller = new LoanController();
    $controller->index();
} elseif ($uri === '/loans/return' && $method === 'POST') {
    $controller = new LoanController();
    $controller->returnBook();
} elseif ($uri === '/reservations' && $method === 'POST') {
    $controller = new ReservationController();
    $controller->reserve();
} elseif ($uri === '/reservations' && $method === 'GET') {
    $controller = new ReservationController();
    $controller->status();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
