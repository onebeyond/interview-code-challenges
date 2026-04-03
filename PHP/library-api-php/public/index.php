<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\Routing\Router;
use App\Infrastructure\Persistence\InMemoryBookStockRepository;
use App\Infrastructure\Persistence\InMemoryFineRepository;
use App\Application\Service\LoanService;
use App\Application\Controller\LoanController;
use App\Application\Controller\ReservationController;

// 1. Initialize the Global State (The "Database")
require_once __DIR__ . '/../data/data.php';

// 2. Instantiate Repositories (The Anti-Corruption Layer)
$bookStockRepo = new InMemoryBookStockRepository();
$fineRepo = new InMemoryFineRepository();

// 3. Instantiate Services (The Domain Logic)
$loanService = new LoanService($bookStockRepo, $fineRepo, 1.50);

// 4. Instantiate Controllers
$loanController = new LoanController($loanService, $bookStockRepo);
$reservationController = new ReservationController();

// 5. Boot the Router and Dispatch
$router = new Router();
$router->registerController($loanController);
$router->registerController($reservationController);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);