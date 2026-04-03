<?php

namespace App\Tests\Unit;

use App\Application\Controller\ReservationController;
use App\Application\Service\ReservationService;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ReservationControllerTest extends TestCase
{
    #[Test]
    public function it_responds_with_a_400_error_when_the_reservation_payload_is_incomplete(): void
    {
        $service = $this->createMock(ReservationService::class);
        $controller = new ReservationController($service);

        ob_start();
        $controller->reserve((string) json_encode(['bookStockId' => 1])); // Missing borrowerId
        $output = ob_get_clean();

        $this->assertStringContainsString('Missing bookStockId or borrowerId', (string)$output);
        $this->assertEquals(400, http_response_code());
    }

    #[Test]
    public function it_executes_a_book_reservation_successfully_and_returns_a_confirmation_message(): void
    {
        $service = $this->createMock(ReservationService::class);
        $service->expects($this->once())->method('reserveBook')->with(5, 10);
        $controller = new ReservationController($service);

        ob_start();
        $controller->reserve((string) json_encode(['bookStockId' => 5, 'borrowerId' => 10]));
        $output = ob_get_clean();

        $this->assertStringContainsString('Book successfully reserved', (string)$output);
    }

    #[Test]
    public function it_responds_with_a_400_error_when_checking_status_without_a_book_id(): void
    {
        $service = $this->createMock(ReservationService::class);
        $controller = new ReservationController($service);
        unset($_GET['bookStockId']);

        ob_start();
        $controller->status();
        $output = ob_get_clean();

        $this->assertStringContainsString('Missing bookStockId', (string)$output);
        $this->assertEquals(400, http_response_code());
    }

    #[Test]
    public function it_calculates_and_returns_the_reservation_status_with_formatted_dates(): void
    {
        $service = $this->createMock(ReservationService::class);
        $service->method('getReservationStatus')->willReturn([
            'queue_length' => 2,
            'estimated_availability' => new DateTimeImmutable('2026-05-01'),
        ]);

        $controller = new ReservationController($service);
        $_GET['bookStockId'] = '5';

        ob_start();
        $controller->status();
        $output = ob_get_clean();

        $this->assertStringContainsString('"queue_length":2', (string)$output);
        $this->assertStringContainsString('"estimated_availability":"2026-05-01"', (string)$output);
    }
}
