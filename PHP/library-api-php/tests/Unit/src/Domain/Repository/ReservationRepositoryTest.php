<?php

namespace App\Tests\Unit;

use App\Domain\Model\Reservation;
use App\Infrastructure\Persistence\InMemoryReservationRepository;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ReservationRepositoryTest extends TestCase
{
    private InMemoryReservationRepository $repo;

    protected function setUp(): void
    {
        $this->repo = new InMemoryReservationRepository();

        // Manual reset to keep tests isolated
        global $reservations;
        $reservations = [];
    }

    #[Test]
    public function it_initializes_the_global_reservation_storage_if_it_is_null_during_save(): void
    {
        global $reservations;

        // FORCE CORRUPTION: Hit lines 14-16
        $reservations = null;

        $res = new Reservation(id: 'res_init', bookStockId: 1, borrowerId: 10, reservedAt: new DateTimeImmutable());

        $this->repo->save($res);

        /** @var mixed $reservations */
        $this->assertIsArray($reservations);
        $this->assertCount(1, $reservations);
    }

    #[Test]
    public function it_returns_an_empty_list_if_the_global_storage_is_corrupted_during_search(): void
    {
        global $reservations;

        // FORCE CORRUPTION: Hit lines 25-27
        $reservations = null;

        $result = $this->repo->findByBookStockId(1);

        $this->assertEmpty($result);
    }

    #[Test]
    public function it_filters_the_global_collection_to_return_only_reservations_for_a_specific_book_stock(): void
    {
        global $reservations;
        $reservations = [];

        $res1 = new Reservation(id: 'res_1', bookStockId: 1, borrowerId: 10, reservedAt: new DateTimeImmutable());
        $res2 = new Reservation(id: 'res_2', bookStockId: 1, borrowerId: 11, reservedAt: new DateTimeImmutable());
        $res3 = new Reservation(id: 'res_3', bookStockId: 2, borrowerId: 12, reservedAt: new DateTimeImmutable());

        $this->repo->save($res1);
        $this->repo->save($res2);
        $this->repo->save($res3);

        $queueForBook1 = $this->repo->findByBookStockId(1);

        $this->assertCount(2, $queueForBook1);
        $this->assertEquals('res_1', $queueForBook1[0]->id);
        $this->assertEquals('res_2', $queueForBook1[1]->id);
    }
}
