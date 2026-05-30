<?php

namespace App\Controllers;

use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use App\Repositories\UserRepositoryInterface;
use App\Middleware\AuthMiddleware;
use App\Models\User;

class UserController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserRepositoryInterface $users
    ) {}

    public function showRegister(Request $request): Response
    {
        return $this->responseFactory->view('user/register.html.twig');
    }

    public function showLogin(Request $request): Response
    {
        return $this->responseFactory->view('user/login.html.twig', [
            'active' => 'login'
        ]);
    }

    public function register(Request $request): Response
    {
        $firstName = $request->get('firstName') ?? '';
        $lastName  = $request->get('lastName') ?? '';
        $email     = $request->get('email') ?? '';
        $password  = $request->get('password') ?? '';

        if (!$firstName || !$lastName || !$email || !$password) {
            return $this->responseFactory->internalError();
        }

        if ($this->users->findByEmail($email)) {
            return $this->responseFactory->internalError();
        }

        $user             = new User();
        $user->firstName  = $firstName;
        $user->lastName   = $lastName;
        $user->email      = $email;
        $user->password   = password_hash($password, PASSWORD_DEFAULT);
        $user->role       = 'user';
        $user->created_at = time();
        $user->deleted_at = null;

        $createdUser = $this->users->create($user);

        if (!$createdUser) {
            return $this->responseFactory
            ->createToast('error', 'Er is iets misgegaan. Probeer het opnieuw.')
            ->internalError();
        }

        return $this->responseFactory
        ->createToast('success', 'Account aangemaakt! Je kunt nu inloggen.')
        ->redirect('/login');
    }

    public function login(Request $request): Response
    {
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
        AuthMiddleware::handle();

        return $this->responseFactory->view('user/overview.html.twig', [
            'active' => 'overview'
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