<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Model\Fine;
use App\Domain\Repository\FineRepositoryInterface;

class InMemoryFineRepository implements FineRepositoryInterface
{
    public function save(Fine $fine): void
    {
        global $fines;

        if (!is_array($fines)) {
            $fines = [];
        }

        $fines[] = $fine;
    }

    public function findByBorrowerId(int $borrowerId): array
    {
        global $fines;

        if (!is_array($fines)) {
            return [];
        }

        return array_filter($fines, function (Fine $fine) use ($borrowerId) {
            return $fine->borrowerId === $borrowerId;
        });
    }
}
