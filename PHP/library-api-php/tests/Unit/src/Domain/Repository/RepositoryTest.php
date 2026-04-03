<?php

namespace App\Tests\Unit;

use App\Domain\Model\BookStock;
use App\Domain\Model\Fine;
use App\Domain\Model\Reservation;
use App\Infrastructure\Persistence\InMemoryBookStockRepository;
use App\Infrastructure\Persistence\InMemoryFineRepository;
use App\Infrastructure\Persistence\InMemoryReservationRepository;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset the global state before every test
        global $bookStocks, $fines, $reservations;
        $bookStocks = [];
        $fines = [];
        $reservations = [];
    }

    #[Test]
    public function it_saves_and_retrieves_active_book_stocks_from_legacy_storage(): void
    {
        $repo = new InMemoryBookStockRepository();
        $stock = new BookStock(id: 1, bookId: 100, isOnLoan: true, loanEndDate: new DateTimeImmutable(), borrowerId: 1);

        $repo->save($stock);

        $this->assertCount(1, $repo->findAllActiveLoans());

        $found = $repo->findById(1);
        $this->assertNotNull($found);
        $this->assertEquals(100, $found->bookId);

        $this->assertNull($repo->findById(999));
    }

    #[Test]
    public function it_persists_generated_overdue_fines_to_the_global_state(): void
    {
        $repo = new InMemoryFineRepository();
        $fine = new Fine(id: 'f1', borrowerId: 1, amount: 15.00, details: 'Overdue fine for 10 days');

        $repo->save($fine);

        global $fines;
        $this->assertCount(1, $fines);
    }

    #[Test]
    public function it_maintains_and_retrieves_the_reservation_queue_for_a_specific_book(): void
    {
        $repo = new InMemoryReservationRepository();
        $res = new Reservation(id: 'r1', bookStockId: 5, borrowerId: 2, reservedAt: new DateTimeImmutable());

        $repo->save($res);

        $queue = $repo->findByBookStockId(5);
        $this->assertCount(1, $queue);
        $this->assertEquals('r1', $queue[0]->id);

        $emptyQueue = $repo->findByBookStockId(999);
        $this->assertEmpty($emptyQueue);
    }
}
