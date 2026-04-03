<?php

namespace App\Tests\Unit;

use App\Domain\Model\BookStock;
use App\Infrastructure\Persistence\InMemoryBookStockRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InfrastructureRepositoryTest extends TestCase
{
    private InMemoryBookStockRepository $repo;

    protected function setUp(): void
    {
        $this->repo = new InMemoryBookStockRepository();

        // Reset the global state to a known clean array before each test
        global $bookStocks;
        $bookStocks = [];
    }

    #[Test]
    public function it_gracefully_handles_uninitialized_or_corrupted_global_state(): void
    {
        global $bookStocks;

        // FORCING CORRUPTION: Set it to null to hit lines 14, 31, and 51
        $bookStocks = null;

        $this->assertNull($this->repo->findById(1));
        $this->assertEmpty($this->repo->findAllActiveLoans());

        // Test the 'save' initialization (Line 32)
        $stock = new BookStock(id: 99, bookId: 1, isOnLoan: false);
        $this->repo->save($stock);

        /** @var mixed $bookStocks */
        $this->assertIsArray($bookStocks);
        $this->assertCount(1, $bookStocks);
    }

    #[Test]
    public function it_updates_an_existing_stock_record_instead_of_appending_a_duplicate(): void
    {
        global $bookStocks;

        $originalStock = new BookStock(id: 1, bookId: 100, isOnLoan: true, loanEndDate: null, borrowerId: 10);
        $this->repo->save($originalStock);

        // Ensure it exists
        $this->assertCount(1, $bookStocks);

        // CREATE UPDATE: Same ID, different state (Line 37 coverage)
        $updatedStock = new BookStock(id: 1, bookId: 100, isOnLoan: false, loanEndDate: null, borrowerId: null);
        $this->repo->save($updatedStock);

        // ASSERTIONS
        $this->assertCount(1, $bookStocks, 'Repository should update existing ID, not append.');
        $this->assertFalse($bookStocks[0]->isOnLoan);
        $this->assertNull($bookStocks[0]->borrowerId);
    }
}
