<?php

namespace App\Application\Service;

use App\Domain\Model\Fine;
use App\Domain\Repository\BookStockRepositoryInterface;
use App\Domain\Repository\FineRepositoryInterface;
use DateTimeImmutable;
use RuntimeException;

readonly class LoanService
{
    public function __construct(
        private BookStockRepositoryInterface $bookStockRepo,
        private FineRepositoryInterface $fineRepo,
        private float $dailyFineRate = 1.50
    ) {
    }

    public function returnBook(int $bookStockId): ?Fine
    {
        $bookStock = $this->bookStockRepo->findById($bookStockId);

        if (!$bookStock || !$bookStock->isOnLoan) {
            throw new RuntimeException('Book stock is not currently on loan.');
        }

        $now = new DateTimeImmutable();
        $fine = null;

        // Calculate fines if overdue
        if ($bookStock->loanEndDate !== null && $now > $bookStock->loanEndDate) {
            $interval = $now->diff($bookStock->loanEndDate);
            $daysLate = $interval->days;

            if ($bookStock->borrowerId === null) {
                throw new \RuntimeException('Cannot process return: Book stock has no associated borrower.');
            }

            if ($daysLate > 0) {
                $fineAmount = $daysLate * $this->dailyFineRate;

                $fine = new Fine(
                    id: uniqid('fine_'),
                    borrowerId: $bookStock->borrowerId,
                    amount: $fineAmount,
                    details: "Late return penalty for $daysLate days."
                );

                $this->fineRepo->save($fine);
            }
        }

        // Domain logic: the model dictates its own state change
        $returnedStock = $bookStock->returnBook();

        $this->bookStockRepo->save($returnedStock);

        return $fine;
    }
}
