<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Model\BookStock;
use App\Domain\Repository\BookStockRepositoryInterface;

class InMemoryBookStockRepository implements BookStockRepositoryInterface
{
    public function findById(int $id): ?BookStock
    {
        global $bookStocks;

        if (!is_array($bookStocks)) {
            return null;
        }

        foreach ($bookStocks as $stock) {
            if ($stock->id === $id) {
                return $stock;
            }
        }

        return null;
    }

    public function save(BookStock $bookStock): void
    {
        global $bookStocks;

        if (!is_array($bookStocks)) {
            $bookStocks = [];
        }

        foreach ($bookStocks as $key => $stock) {
            if ($stock->id === $bookStock->id) {
                $bookStocks[$key] = $bookStock;

                return;
            }
        }

        // If it doesn't exist, append it
        $bookStocks[] = $bookStock;
    }

    public function findAllActiveLoans(): array
    {
        global $bookStocks;

        if (!is_array($bookStocks)) {
            return [];
        }

        return array_filter($bookStocks, fn (BookStock $stock) => $stock->isOnLoan);
    }
}
