<?php

namespace App\Domain\Repository;

use App\Domain\Model\Book;

interface BookRepositoryInterface
{
    /**
     * @return Book[]
     */
    public function findAll(): array;
}
