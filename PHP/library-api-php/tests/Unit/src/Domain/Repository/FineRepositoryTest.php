<?php

namespace App\Tests\Unit;

use App\Domain\Model\Fine;
use App\Infrastructure\Persistence\InMemoryFineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FineRepositoryTest extends TestCase
{
    private InMemoryFineRepository $repo;

    protected function setUp(): void
    {
        $this->repo = new InMemoryFineRepository();

        // Ensure we start fresh
        global $fines;
        $fines = [];
    }

    #[Test]
    public function it_initializes_the_global_fine_storage_if_it_is_not_currently_an_array(): void
    {
        global $fines;

        // FORCE CORRUPTION: Set to null to hit lines 14-16
        $fines = null;

        $fine = new Fine(id: 'f_test', borrowerId: 1, amount: 10.50, details: 'Initial test fine');

        // WHEN: We save a fine to a corrupted/uninitialized global state
        $this->repo->save($fine);

        // THEN: The repository should have initialized it to an array and appended the fine
        /** @var mixed $fines */
        $this->assertIsArray($fines);
        $this->assertCount(1, $fines);
        $this->assertSame($fine, $fines[0]);
    }

    #[Test]
    public function it_persists_multiple_fines_by_appending_them_to_the_global_collection(): void
    {
        global $fines;
        $fines = []; // Start with an empty array

        $fine1 = new Fine(id: 'f_1', borrowerId: 1, amount: 5.00);
        $fine2 = new Fine(id: 'f_2', borrowerId: 2, amount: 15.00);

        $this->repo->save($fine1);
        $this->repo->save($fine2);

        $this->assertCount(2, $fines);
    }
}
