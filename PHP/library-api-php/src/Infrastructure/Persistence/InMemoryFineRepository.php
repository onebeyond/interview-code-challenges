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
}