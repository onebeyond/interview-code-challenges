<?php

namespace App\Tests\Unit;

use App\Application\Service\ReservationService;
use App\Domain\Model\BookStock;
use App\Domain\Model\Reservation;
use App\Domain\Repository\BookStockRepositoryInterface;
use App\Domain\Repository\ReservationRepositoryInterface;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ReservationServiceTest extends TestCase
{
    private BookStockRepositoryInterface&MockObject $bookStockRepo;
    private ReservationRepositoryInterface&MockObject $reservationRepo;
    private ReservationService $reservationService;

    protected function setUp(): void
    {
        $this->bookStockRepo = $this->createMock(BookStockRepositoryInterface::class);
        $this->reservationRepo = $this->createMock(ReservationRepositoryInterface::class);
        
        // Injecting a 14-day standard loan period for queue math
        $this->reservationService = new ReservationService(
            $this->bookStockRepo, 
            $this->reservationRepo, 
            14
        );
    }

    #[Test]
    public function it_prevents_reserving_a_book_that_is_not_on_loan(): void
    {
        $bookStock = new BookStock(id: 1, bookId: 100, isOnLoan: false);
        
        $this->bookStockRepo->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($bookStock);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("You can only reserve books that are currently on loan.");

        $this->reservationService->reserveBook(bookStockId: 1, borrowerId: 42);
    }

    #[Test]
    public function it_calculates_correct_status_for_second_person_in_queue(): void
    {
        // Current loan ends in 5 days
        $loanEnd = (new DateTimeImmutable())->modify('+5 days');
        $bookStock = new BookStock(id: 1, bookId: 100, isOnLoan: true, loanEndDate: $loanEnd, borrowerId: 99);
        
        $this->bookStockRepo->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($bookStock);

        // There is already 1 person in the queue ahead of our borrower
        $existingReservation = new Reservation(id: 'res_1', bookStockId: 1, borrowerId: 10, reservedAt: new DateTimeImmutable());
        
        $this->reservationRepo->expects($this->once())
            ->method('findByBookStockId')
            ->with(1)
            ->willReturn([$existingReservation]);

        // WHEN we query the status for the new queue position (Position 1, since arrays are 0-indexed)
        $status = $this->reservationService->getReservationStatus(bookStockId: 1);

        // THEN: The estimated date should be the original loan end + 1 standard loan period (14 days)
        $expectedDate = $loanEnd->modify('+14 days')->format('Y-m-d');
        
        $this->assertEquals(2, $status['queue_length']);
        $this->assertEquals($expectedDate, $status['estimated_availability']->format('Y-m-d'));
    }
}