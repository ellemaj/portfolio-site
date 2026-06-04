<?php

namespace Tests\Unit\Controllers;

use App\Controllers\UserController;
use App\Repositories\UserRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    private ResponseFactory $responseFactory;
    private UserRepositoryInterface $users;
    private Session $session;
    private UserController $controller;

    protected function setUp(): void
    {
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->users = $this->createMock(UserRepositoryInterface::class);
        $this->session = $this->createMock(Session::class);
        $this->controller = new UserController($this->responseFactory, $this->users, $this->session);
    }

    private function makeRequest(array $query = [], array $post = []): Request
    {
        return new Request('GET', '/', $query, $post);
    }

    public function test_show_login_returns_response(): void
    {
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->showLogin($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_show_register_returns_response(): void
    {
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->showRegister($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_overview_returns_response(): void
    {
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->overview($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_register_redirects_when_email_is_invalid(): void
    {
        $this->responseFactory->method('createToast')->willReturn($this->responseFactory);
        $this->responseFactory->method('redirect')->willReturn(new Response('', 302));

        $request = $this->makeRequest([], [
            'firstName' => 'Test',
            'lastName'  => 'User',
            'email'     => 'geen-geldig-email',
            'password'  => 'wachtwoord123',
        ]);

        $response = $this->controller->register($request);

        $this->assertEquals(302, $response->responseCode);
    }

    public function test_register_redirects_when_password_too_short(): void
    {
        $this->responseFactory->method('createToast')->willReturn($this->responseFactory);
        $this->responseFactory->method('redirect')->willReturn(new Response('', 302));

        $request = $this->makeRequest([], [
            'firstName' => 'Test',
            'lastName'  => 'User',
            'email'     => 'test@example.com',
            'password'  => 'kort',
        ]);

        $response = $this->controller->register($request);

        $this->assertEquals(302, $response->responseCode);
    }
}
