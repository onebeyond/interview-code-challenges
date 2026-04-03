<?php

namespace App\Application\Controller;

use App\Application\Routing\Route;
use App\Application\Service\LoanService;
use App\Domain\Repository\BookStockRepositoryInterface;

readonly class LoanController
{
    public function __construct(
        private LoanService $loanService,
        private BookStockRepositoryInterface $bookStockRepo
    ) {
    }

    #[Route('/loans', 'GET')]
    public function index(): void
    {
        $activeLoans = $this->bookStockRepo->findAllActiveLoans();
        echo json_encode($activeLoans);
    }

    #[Route('/loans/return', 'POST')]
    public function returnBook(?string $payload = null): void
    {
        $payload = $payload ?? file_get_contents('php://input');
        $data = json_decode((string) $payload, true);

        if (!isset($data['bookStockId'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing bookStockId in request body']);

            return;
        }

        $fine = $this->loanService->returnBook((int) $data['bookStockId']);

        // Fetch the updated state
        $updatedStock = $this->bookStockRepo->findById((int) $data['bookStockId']);

        echo json_encode([
            'message' => 'Book returned successfully.',
            'fine_generated' => $fine,
            'updated_stock' => $updatedStock,
        ]);
    }
}
