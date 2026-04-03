<?php

namespace App\Domain\Repository;

use App\Domain\Model\Fine;

interface FineRepositoryInterface
{
    public function save(Fine $fine): void;
}
