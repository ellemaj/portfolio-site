<?php

namespace App\Controllers;

use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use Framework\Session;

class UserController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserRepositoryInterface $users,
        private Session $session,
        private PostRepositoryInterface $posts,
        private ProjectRepositoryInterface $projects,
    ) {
    }

    public function showLogin(Request $request): Response
    {
        return $this->responseFactory->view('user/login.html.twig', [
            'active' => 'login'
        ]);
    }

    public function admin(Request $request): Response
    {
        $target = $this->session->get('role') === 'admin' ? '/overview' : '/login';
        return $this->responseFactory->redirect($target);
    }

    public function login(Request $request): Response
    {
        $token = $request->get('csrf_token') ?? '';
        if (!$this->session->validateCsrfToken($token)) {
            return $this->responseFactory
                ->createToast('error', 'Ongeldig verzoek. Probeer opnieuw.')
                ->redirect('/login');
        }

        $email    = $request->get('email') ?? '';
        $password = $request->get('password') ?? '';

        $user = $this->users->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user->id;
            $_SESSION['role']      = $user->role;
            $_SESSION['firstName'] = $user->firstName;
            $_SESSION['lastName']  = $user->lastName;

            return $this->responseFactory
            ->createToast('success', 'Welkom terug, ' . $user->firstName . '!')
            ->redirect('/overview');
        }

        return $this->responseFactory
        ->createToast('error', 'E-mailadres of wachtwoord klopt niet.')
        ->redirect('/login');
    }

    public function overview(Request $request): Response
    {
        $publishedPosts = $this->posts->findAllPublished();

        return $this->responseFactory->view('user/overview.html.twig', [
            'active'      => 'overview',
            'postCount'   => count($publishedPosts),
            'recentPosts' => array_slice($publishedPosts, 0, 3),
            'projects'    => $this->projects->findAll(),
        ]);
    }

    public function logout(Request $request): Response
    {
        $toasts = [['message' => 'Je bent uitgelogd.']];
        session_destroy();

        session_start();
        $_SESSION['_toasts'] = $toasts;

        return $this->responseFactory->redirect('/login');
    }
}
