<?php

namespace App\Application\Controller;

use App\Application\Routing\Route;

readonly class ReservationController 
{
    #[Route('/reservations', 'POST')]
    public function reserve(): void 
    {
        http_response_code(501);
        echo json_encode(['message' => '']);
    }

    #[Route('/reservations/status', 'GET')]
    public function status(): void 
    {
        http_response_code(501);
        echo json_encode(['message' => '']);
    }
}