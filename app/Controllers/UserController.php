<?php

namespace App\Controllers;

use Exception;
use Framework\Response;
use Framework\ResponseFactory;
use App\Repositories\UserRepositoryInterface;
use App\Middleware\AuthMiddleware;

class UserController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserRepositoryInterface $users
    ) {}

    public function showRegister(): Response
    {
        return $this->responseFactory->view('user/register.html.twig');
    }

    public function showLogin(): Response
    {
        return $this->responseFactory->view('user/login.html.twig', [
            'active' => 'login'
        ]);
    }

    public function register(): void
    {
        $firstName = $_POST['firstName'] ?? '';
        $lastName     = $_POST['lastName'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $this->users->create($firstName, $lastName, $email, $hashedPassword);
        } catch (\PDOException $e) {
            exit('Email already exists');
        }

        header('Location: /login');
        exit;
    }

    public function login(): void
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->users->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role']    = $user->role;
            $_SESSION['firstName'] = $user->firstName;
            $_SESSION['lastName'] = $user->lastName;

            header('Location: /overview');
            exit;
        }

        exit('Invalid credentials');
    }

    public function overview(): Response
    {
        AuthMiddleware::handle();

        return $this->responseFactory->view('user/overview.html.twig', [
            'active' => 'overview'
        ]);
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}