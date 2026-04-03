<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\Routing\Router;
use App\Infrastructure\Persistence\InMemoryBookStockRepository;
use App\Infrastructure\Persistence\InMemoryFineRepository;
use App\Infrastructure\Persistence\InMemoryReservationRepository;
use App\Application\Service\LoanService;
use App\Application\Service\ReservationService;
use App\Application\Controller\LoanController;
use App\Application\Controller\ReservationController;

// 1. Initialize the Global State (The "Database")
require_once __DIR__ . '/../data/data.php';

// 2. Instantiate Repositories
$bookStockRepo = new InMemoryBookStockRepository();
$fineRepo = new InMemoryFineRepository();
$reservationRepo = new InMemoryReservationRepository();

// 3. Instantiate Services
$loanService = new LoanService($bookStockRepo, $fineRepo, 1.50);
$reservationService = new ReservationService($bookStockRepo, $reservationRepo, 14);

// 4. Instantiate Controllers
$loanController = new LoanController($loanService, $bookStockRepo);
$reservationController = new ReservationController($reservationService);

// 5. Boot the Router and Dispatch
$router = new Router();
$router->registerController($loanController);
$router->registerController($reservationController);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);