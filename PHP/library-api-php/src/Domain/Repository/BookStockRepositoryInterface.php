<?php

namespace App\Domain\Repository;

use App\Domain\Model\BookStock;

interface BookStockRepositoryInterface 
{
    public function findById(int $id): ?BookStock;
    public function save(BookStock $bookStock): void;
    
    /**
     * @return BookStock[]
     */
    public function findAllActiveLoans(): array;
}