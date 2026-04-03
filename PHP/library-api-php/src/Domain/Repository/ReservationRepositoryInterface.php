<?php

namespace App\Domain\Repository;

use App\Domain\Model\Reservation;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void;

    /**
     * @return Reservation[]
     */
    public function findByBookStockId(int $bookStockId): array;
}
