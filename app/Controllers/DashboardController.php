<?php

namespace App\Controllers;

use App\Middleware\AdminMiddleware;
use App\Repositories\CourseRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class DashboardController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private CourseRepositoryInterface $courses,
        private AdminMiddleware $adminMiddleware
    ) {}

    public function index(Request $request): Response
    {
        $courses  = $this->courses->findAll();
        $earned   = array_sum(array_map(
            fn($c) => ($c->grade !== null && $c->grade >= 5.5) ? $c->ec : 0,
            $courses
        ));
        $total    = array_sum(array_map(fn($c) => $c->ec, $courses));

        return $this->responseFactory->view('dashboard.html.twig', [
            'active'   => 'dashboard',
            'courses'  => $courses,
            'earned'   => $earned,
            'total'    => $total,
        ]);
    }

    public function updateGrade(Request $request): Response
    {
        if ($response = $this->adminMiddleware->handle()) return $response;

        $id    = (int) $request->get('id');
        $grade = $request->get('grade');

        $this->courses->updateGrade($id, $grade !== null ? (float) $grade : null);

        return $this->responseFactory->redirect('/dashboard');
    }
}