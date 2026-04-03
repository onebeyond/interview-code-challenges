<?php

namespace App\Application\Service;

use App\Domain\Model\Reservation;
use App\Domain\Repository\BookStockRepositoryInterface;
use App\Domain\Repository\ReservationRepositoryInterface;
use DateTimeImmutable;
use RuntimeException;

readonly class ReservationService
{
    public function __construct(
        private BookStockRepositoryInterface $bookStockRepo,
        private ReservationRepositoryInterface $reservationRepo,
        private int $standardLoanDays = 14
    ) {
    }

    public function reserveBook(int $bookStockId, int $borrowerId): void
    {
        $bookStock = $this->bookStockRepo->findById($bookStockId);

        if (!$bookStock || !$bookStock->isOnLoan) {
            throw new RuntimeException('You can only reserve books that are currently on loan.');
        }

        $reservation = new Reservation(
            id: uniqid('res_'),
            bookStockId: $bookStockId,
            borrowerId: $borrowerId,
            reservedAt: new DateTimeImmutable()
        );

        $this->reservationRepo->save($reservation);
    }

    /**
     * @return array{queue_length: int, estimated_availability: DateTimeImmutable}
     */
    public function getReservationStatus(int $bookStockId): array
    {
        $bookStock = $this->bookStockRepo->findById($bookStockId);

        if (!$bookStock || !$bookStock->isOnLoan || !$bookStock->loanEndDate) {
            throw new RuntimeException('Book is not currently on loan.');
        }

        $queue = $this->reservationRepo->findByBookStockId($bookStockId);

        // If there are 2 people already waiting, the new person is position 2 (0-indexed logic)
        $queuePosition = count($queue);

        // Date math: Current end date + (Queue Position * Standard Loan Duration)
        $daysToAdd = $queuePosition * $this->standardLoanDays;
        $estimatedDate = $bookStock->loanEndDate->modify("+$daysToAdd days");

        return [
            'queue_length' => $queuePosition + 1, // Total people including the person querying
            'estimated_availability' => $estimatedDate,
        ];
    }
}
