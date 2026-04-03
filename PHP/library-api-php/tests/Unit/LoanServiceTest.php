<?php

namespace App\Tests\Unit;

use App\Domain\Model\BookStock;
use App\Domain\Model\Fine;
use App\Application\Service\LoanService;
use App\Domain\Repository\BookStockRepositoryInterface;
use App\Domain\Repository\FineRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use DateTimeImmutable;

class LoanServiceTest extends TestCase
{
    private BookStockRepositoryInterface&MockObject $bookStockRepo;
    private FineRepositoryInterface&MockObject $fineRepo;
    private LoanService $loanService;

    protected function setUp(): void
    {
        // GIVEN the required infrastructure contracts
        $this->bookStockRepo = $this->createMock(BookStockRepositoryInterface::class);
        $this->fineRepo = $this->createMock(FineRepositoryInterface::class);
        
        // Injecting a strict fine rate of £1.50 per overdue day
        $this->loanService = new LoanService($this->bookStockRepo, $this->fineRepo, 1.50);
    }

    #[Test]
    public function it_charges_a_fine_when_a_book_is_returned_late(): void
    {
        // GIVEN: A book that was due 5 days ago
        $fiveDaysAgo = (new DateTimeImmutable())->modify('-5 days');
        $bookStock = new BookStock(
            id: 1, 
            bookId: 100, 
            isOnLoan: true, 
            loanEndDate: $fiveDaysAgo, 
            borrowerId: 42
        );
        
        $this->bookStockRepo->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($bookStock);

        // THEN: Expect the repository to save the updated, immutable stock (not on loan)
        $this->bookStockRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(BookStock $bs) => $bs->isOnLoan === false && $bs->borrowerId === null));

        // THEN: Expect a fine of £7.50 (5 days * £1.50) to be generated and saved
        $this->fineRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Fine $fine) => $fine->amount === 7.50 && $fine->borrowerId === 42));

        // WHEN: The borrower returns the book
        $this->loanService->returnBook(bookStockId: 1);
    }
    
    #[Test]
    public function it_does_not_charge_a_fine_when_returned_on_time(): void
    {
        // GIVEN: A book due tomorrow
        $tomorrow = (new DateTimeImmutable())->modify('+1 day');
        $bookStock = new BookStock(
            id: 2, 
            bookId: 101, 
            isOnLoan: true, 
            loanEndDate: $tomorrow, 
            borrowerId: 42
        );
        
        $this->bookStockRepo->expects($this->once())
            ->method('findById')
            ->with(2)
            ->willReturn($bookStock);
            
        $this->bookStockRepo->expects($this->once())
            ->method('save');
            
        // THEN: No fine should ever be saved
        $this->fineRepo->expects($this->never())
            ->method('save');

        // WHEN: The book is returned
        $this->loanService->returnBook(bookStockId: 2);
    }
}