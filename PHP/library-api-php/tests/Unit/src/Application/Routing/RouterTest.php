<?php

namespace App\Tests\Unit;

use App\Application\Routing\Route;
use App\Application\Routing\Router;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DummyController
{
    #[Route('/test', 'GET')]
    public function successRoute(): void
    {
        echo json_encode(['status' => 'ok']);
    }

    #[Route('/fail', 'GET')]
    public function failRoute(): void
    {
        throw new RuntimeException('Domain violation simulated');
    }

    #[Route('/crash', 'GET')]
    public function crashRoute(): void
    {
        throw new Exception('System crash simulated');
    }
}

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->registerController(new DummyController());
    }

    #[Test]
    public function it_dispatches_successful_routes_and_returns_200(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/test');
        $output = ob_get_clean();

        $this->assertEquals('{"status":"ok"}', $output);
    }

    #[Test]
    public function it_returns_404_when_an_endpoint_is_not_found(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/nowhere');
        $output = ob_get_clean();

        $this->assertStringContainsString('Endpoint not found', (string) $output);
        $this->assertEquals(404, http_response_code());
    }

    #[Test]
    public function it_catches_domain_exceptions_and_returns_400_bad_request(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/fail');
        $output = ob_get_clean();

        $this->assertStringContainsString('Domain violation simulated', (string) $output);
        $this->assertEquals(400, http_response_code());
    }

    #[Test]
    public function it_catches_unexpected_system_exceptions_and_returns_500_internal_server_error(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/crash');
        $output = ob_get_clean();

        $this->assertStringContainsString('Internal Server Error', (string) $output);
        $this->assertEquals(500, http_response_code());
    }
}
