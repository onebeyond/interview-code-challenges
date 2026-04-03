<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Model\Reservation;
use App\Domain\Repository\ReservationRepositoryInterface;

class InMemoryReservationRepository implements ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void
    {
        global $reservations;

        if (!is_array($reservations)) {
            $reservations = [];
        }

        $reservations[] = $reservation;
    }

    public function findByBookStockId(int $bookStockId): array
    {
        global $reservations;

        if (!is_array($reservations)) {
            return [];
        }

        return array_filter($reservations, fn (Reservation $r) => $r->bookStockId === $bookStockId);
    }
}
