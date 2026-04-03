<?php

namespace App\Tests\Unit;

use App\Application\Controller\BookController;
use App\Domain\Repository\BookRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BookControllerTest extends TestCase
{
    #[Test]
    public function it_provides_the_entire_book_catalog_as_json(): void
    {
        $repo = $this->createMock(BookRepositoryInterface::class);
        $repo->method('findAll')->willReturn([]);

        $controller = new BookController($repo);

        ob_start();
        $controller->index();
        $output = ob_get_clean();

        $this->assertEquals('[]', $output);
    }
}
