<?php

namespace App\Application\Controller;

use App\Application\Routing\Route;
use App\Application\Service\ReservationService;

readonly class ReservationController
{
    public function __construct(
        private ReservationService $reservationService
    ) {
    }

    #[Route('/reservations', 'POST')]
    public function reserve(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['bookStockId']) || !isset($data['borrowerId'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing bookStockId or borrowerId in request body']);

            return;
        }

        $this->reservationService->reserveBook((int) $data['bookStockId'], (int) $data['borrowerId']);

        echo json_encode(['message' => 'Book successfully reserved.']);
    }

    #[Route('/reservations/status', 'GET')]
    public function status(): void
    {
        $bookStockId = $_GET['bookStockId'] ?? null;

        if (!$bookStockId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing bookStockId parameter in query string']);

            return;
        }

        $status = $this->reservationService->getReservationStatus((int) $bookStockId);

        // Convert DateTimeImmutable to a readable string format before sending JSON
        $status['estimated_availability'] = $status['estimated_availability']->format('Y-m-d');

        echo json_encode($status);
    }
}
