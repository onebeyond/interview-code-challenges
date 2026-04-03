<?php

namespace App\Tests\Unit;

use App\Domain\Model\Book;
use App\Domain\Model\BookStock;
use App\Infrastructure\Persistence\InMemoryBookRepository;
use App\Infrastructure\Persistence\InMemoryBookStockRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InfrastructureRepositoryTest extends TestCase
{
    private InMemoryBookStockRepository $stockRepo;
    private InMemoryBookRepository $bookRepo;

    protected function setUp(): void
    {
        $this->stockRepo = new InMemoryBookStockRepository();
        $this->bookRepo = new InMemoryBookRepository();

        // Reset all global state to prevent test pollution
        global $bookStocks, $books;
        $bookStocks = [];
        $books = [];
    }

    #[Test]
    public function it_returns_an_empty_list_from_book_repository_if_global_state_is_not_an_array(): void
    {
        global $books;

        // GIVEN: The global state is corrupted or uninitialized
        /** @var mixed $books */
        $books = null;

        // WHEN: We attempt to fetch the catalog
        $result = $this->bookRepo->findAll();

        // THEN: It should return an empty array instead of crashing
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_retrieves_the_complete_book_catalog_from_global_storage(): void
    {
        global $books;

        // GIVEN: A set of books in the legacy storage
        $books = [
            new Book(id: 1, title: 'Test Book', authorId: 1, format: 'Ebook', isbn: '123'),
        ];

        // WHEN: We fetch the catalog
        $result = $this->bookRepo->findAll();

        // THEN: We get the exact collection back
        $this->assertCount(1, $result);
        $this->assertEquals('Test Book', $result[0]->title);
    }

    #[Test]
    public function it_gracefully_handles_uninitialized_stock_state_and_initializes_on_save(): void
    {
        global $bookStocks;

        // GIVEN: Global state is null
        /** @var mixed $bookStocks */
        $bookStocks = null;

        $this->assertNull($this->stockRepo->findById(1));
        $this->assertEmpty($this->stockRepo->findAllActiveLoans());

        // WHEN: We save to the uninitialized state
        $stock = new BookStock(id: 99, bookId: 1, isOnLoan: false);
        $this->stockRepo->save($stock);

        // THEN: It initializes the array and persists
        /** @var mixed $bookStocks */
        $this->assertIsArray($bookStocks);
        $this->assertCount(1, $bookStocks);
    }

    #[Test]
    public function it_updates_an_existing_stock_record_instead_of_appending_a_duplicate(): void
    {
        global $bookStocks;

        $originalStock = new BookStock(id: 1, bookId: 100, isOnLoan: true, loanEndDate: null, borrowerId: 10);
        $this->stockRepo->save($originalStock);

        $this->assertCount(1, $bookStocks);

        // WHEN: We save a stock with the same ID but different data
        $updatedStock = new BookStock(id: 1, bookId: 100, isOnLoan: false, loanEndDate: null, borrowerId: null);
        $this->stockRepo->save($updatedStock);

        // THEN: The record is updated in place
        $this->assertCount(1, $bookStocks, 'Repository should update existing ID, not append.');
        $this->assertFalse($bookStocks[0]->isOnLoan);
        $this->assertNull($bookStocks[0]->borrowerId);
    }
}
