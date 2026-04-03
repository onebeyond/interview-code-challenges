<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\BookRepositoryInterface;

class InMemoryBookRepository implements BookRepositoryInterface
{
    public function findAll(): array
    {
        global $books;

        return is_array($books) ? $books : [];
    }
}
