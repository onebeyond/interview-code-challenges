<?php

namespace App\Tests\Unit;

use App\Application\Controller\LoanController;
use App\Application\Service\LoanService;
use App\Domain\Repository\BookStockRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoanControllerTest extends TestCase
{
    #[Test]
    public function it_provides_a_list_of_all_active_loans_as_json(): void
    {
        $repo = $this->createMock(BookStockRepositoryInterface::class);
        $repo->method('findAllActiveLoans')->willReturn([]);
        $service = $this->createMock(LoanService::class);
        $controller = new LoanController($service, $repo);

        ob_start();
        $controller->index();
        $output = ob_get_clean();

        $this->assertEquals('[]', $output);
    }

    #[Test]
    public function it_responds_with_a_400_error_when_the_return_payload_is_missing_data(): void
    {
        $repo = $this->createMock(BookStockRepositoryInterface::class);
        $service = $this->createMock(LoanService::class);
        $controller = new LoanController($service, $repo);

        ob_start();
        $controller->returnBook((string) json_encode([]));
        $output = ob_get_clean();

        $this->assertStringContainsString('Missing bookStockId', (string)$output);
        $this->assertEquals(400, http_response_code());
    }

    #[Test]
    public function it_processes_a_valid_book_return_and_confirms_success_via_json(): void
    {
        $repo = $this->createMock(BookStockRepositoryInterface::class);
        $service = $this->createMock(LoanService::class);
        $service->expects($this->once())->method('returnBook')->with(123);

        $controller = new LoanController($service, $repo);

        ob_start();
        $controller->returnBook((string) json_encode(['bookStockId' => 123]));
        $output = ob_get_clean();

        $this->assertStringContainsString('Book returned successfully', (string)$output);
    }
}
