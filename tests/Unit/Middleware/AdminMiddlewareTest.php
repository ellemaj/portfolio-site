<?php

namespace Tests\Unit\Middleware;

use App\Middleware\AdminMiddleware;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\TestCase;

class AdminMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function test_returns_forbidden_when_no_session(): void
    {
        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->method('forbidden')
            ->willReturn(new Response('Forbidden', 403));

        $middleware = new AdminMiddleware($responseFactory);
        $result = $middleware->handle();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(403, $result->responseCode);
    }

    public function test_returns_null_when_user_is_admin(): void
    {
        $_SESSION['role'] = 'admin';

        $responseFactory = $this->createMock(ResponseFactory::class);
        $middleware = new AdminMiddleware($responseFactory);

        $result = $middleware->handle();

        $this->assertNull($result);
    }

    public function test_returns_forbidden_when_user_has_role_user(): void
    {
        $_SESSION['role'] = 'user';

        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->method('forbidden')
            ->willReturn(new Response('Forbidden', 403));

        $middleware = new AdminMiddleware($responseFactory);
        $result = $middleware->handle();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(403, $result->responseCode);
    }
}
