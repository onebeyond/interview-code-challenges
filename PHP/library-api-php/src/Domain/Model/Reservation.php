<?php

namespace App\Domain\Model;

use DateTimeImmutable;

readonly class Reservation 
{
    public function __construct(
        public string $id, // Use UUIDs or strings for IDs to prevent DB collision
        public int $bookId,
        public int $borrowerId,
        public DateTimeImmutable $reservedAt
    ) {}
}