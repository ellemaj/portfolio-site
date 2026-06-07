<?php

namespace Tests\Unit\Controllers;

use App\Controllers\ProjectController;
use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\TestCase;

class ProjectControllerTest extends TestCase
{
    private ResponseFactory $responseFactory;
    private ProjectRepositoryInterface $projects;
    private ProjectController $controller;

    protected function setUp(): void
    {
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->projects = $this->createMock(ProjectRepositoryInterface::class);
        $this->controller = new ProjectController($this->responseFactory, $this->projects);
    }

    private function makeRequest(array $query = [], array $post = []): Request
    {
        return new Request('GET', '/', $query, $post);
    }

    private function makeProject(): Project
    {
        $project = new Project();
        $project->id = 1;
        $project->name = 'Test Project';
        $project->description = 'Een testbeschrijving.';
        $project->url = null;
        $project->sort_order = 0;
        $project->created_at = time();
        return $project;
    }

    public function test_manage_returns_response(): void
    {
        $this->projects->method('findAll')->willReturn([]);
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->manage($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_show_create_returns_response(): void
    {
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->showCreate($this->makeRequest());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->responseCode);
    }

    public function test_create_returns_error_when_name_missing(): void
    {
        $this->responseFactory->method('internalError')->willReturn(new Response('', 500));

        $response = $this->controller->create($this->makeRequest([], [
            'description' => 'Een beschrijving.',
        ]));

        $this->assertEquals(500, $response->responseCode);
    }

    public function test_create_returns_error_when_description_missing(): void
    {
        $this->responseFactory->method('internalError')->willReturn(new Response('', 500));

        $response = $this->controller->create($this->makeRequest([], [
            'name' => 'Mijn Project',
        ]));

        $this->assertEquals(500, $response->responseCode);
    }

    public function test_create_returns_redirect_on_success(): void
    {
        $this->projects->method('create')->willReturn($this->makeProject());
        $this->responseFactory->method('createToast')->willReturn($this->responseFactory);
        $this->responseFactory->method('redirect')->willReturn(new Response('', 302));

        $response = $this->controller->create($this->makeRequest([], [
            'name'        => 'Mijn Project',
            'description' => 'Een beschrijving.',
        ]));

        $this->assertEquals(302, $response->responseCode);
    }

    public function test_show_edit_returns_not_found_when_project_missing(): void
    {
        $this->projects->method('findById')->willReturn(null);
        $this->responseFactory->method('notFound')->willReturn(new Response('', 404));

        $response = $this->controller->showEdit($this->makeRequest(['id' => '999']));

        $this->assertEquals(404, $response->responseCode);
    }

    public function test_show_edit_returns_view_when_project_found(): void
    {
        $this->projects->method('findById')->willReturn($this->makeProject());
        $this->responseFactory->method('view')->willReturn(new Response('', 200));

        $response = $this->controller->showEdit($this->makeRequest(['id' => '1']));

        $this->assertEquals(200, $response->responseCode);
    }

    public function test_update_returns_not_found_when_project_missing(): void
    {
        $this->projects->method('findById')->willReturn(null);
        $this->responseFactory->method('notFound')->willReturn(new Response('', 404));

        $response = $this->controller->update($this->makeRequest(['id' => '999'], [
            'name'        => 'Nieuw',
            'description' => 'Beschrijving',
        ]));

        $this->assertEquals(404, $response->responseCode);
    }

    public function test_update_returns_error_when_name_missing(): void
    {
        $this->projects->method('findById')->willReturn($this->makeProject());
        $this->responseFactory->method('internalError')->willReturn(new Response('', 500));

        $response = $this->controller->update($this->makeRequest(['id' => '1'], [
            'description' => 'Beschrijving',
        ]));

        $this->assertEquals(500, $response->responseCode);
    }

    public function test_update_returns_redirect_on_success(): void
    {
        $this->projects->method('findById')->willReturn($this->makeProject());
        $this->projects->method('update')->willReturn($this->makeProject());
        $this->responseFactory->method('createToast')->willReturn($this->responseFactory);
        $this->responseFactory->method('redirect')->willReturn(new Response('', 302));

        $response = $this->controller->update($this->makeRequest(['id' => '1'], [
            'name'        => 'Bijgewerkt',
            'description' => 'Nieuwe beschrijving.',
        ]));

        $this->assertEquals(302, $response->responseCode);
    }

    public function test_delete_returns_error_when_repository_fails(): void
    {
        $this->projects->method('delete')->willReturn(false);
        $this->responseFactory->method('internalError')->willReturn(new Response('', 500));

        $response = $this->controller->delete($this->makeRequest(['id' => '1']));

        $this->assertEquals(500, $response->responseCode);
    }

    public function test_delete_returns_redirect_on_success(): void
    {
        $this->projects->method('delete')->willReturn(true);
        $this->responseFactory->method('createToast')->willReturn($this->responseFactory);
        $this->responseFactory->method('redirect')->willReturn(new Response('', 302));

        $response = $this->controller->delete($this->makeRequest(['id' => '1']));

        $this->assertEquals(302, $response->responseCode);
    }
}
