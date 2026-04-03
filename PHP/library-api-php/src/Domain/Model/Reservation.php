<?php

namespace App\Domain\Model;

use DateTimeImmutable;

readonly class Reservation
{
    public function __construct(
        public string $id,
        public int $bookStockId,
        public int $borrowerId,
        public DateTimeImmutable $reservedAt
    ) {
    }
}
