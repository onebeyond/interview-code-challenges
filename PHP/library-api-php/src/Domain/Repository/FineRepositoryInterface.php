<?php

namespace App\Domain\Repository;

use App\Domain\Model\Fine;

interface FineRepositoryInterface
{
    public function save(Fine $fine): void;

    /** @return Fine[] */
    public function findByBorrowerId(int $borrowerId): array;
}
