<?php

namespace Tests\Unit\Controllers;

use App\Controllers\HomeController;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private ResponseFactory $responseFactory;
    private HomeController $controller;
    private Request $request;

    protected function setUp(): void
    {
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->controller = new HomeController($this->responseFactory);
        $this->request = new Request('GET', '/', [], []);
    }

    public function test_index_returns_response(): void
    {
        $this->responseFactory->method('view')
            ->willReturn(new Response('', 200));

        $response = $this->controller->index($this->request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_faq_returns_response(): void
    {
        $this->responseFactory->method('view')
            ->willReturn(new Response('', 200));

        $response = $this->controller->faq($this->request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_commandmaker_returns_response(): void
    {
        $this->responseFactory->method('view')
            ->willReturn(new Response('', 200));

        $response = $this->controller->commandmaker($this->request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }
}
