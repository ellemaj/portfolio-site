<?php

namespace App\Controllers;

use Exception;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Database;
use App\Middleware\AuthMiddleware;

class UserController
{
    private ResponseFactory $responseFactory;
    private Database $database;

    public function __construct(ResponseFactory $responseFactory, Database $database)
    {
        $this->responseFactory = $responseFactory;
        $this->database = $database;
    }

    /**
     * @throws Exception
     */
    public function showRegister(): Response
    {
        return $this->responseFactory->view('user/register.html.twig');
    }

    /**
     * @throws Exception
     */
    public function showLogin(): Response
    {
        return $this->responseFactory->view('user/login.html.twig', [
            'active' => 'login'
        ]);
    }

    public function register(): void
    {
        $username = $_POST['username'] ?? '';
        $name = $_POST['name'] ?? '';
        $email =  $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->database->prepare("
            INSERT INTO users (
                username,
                name,
                email,
                password
            )
            VALUES (?, ?, ?, ?)
        ");

        try {
            $stmt->execute([
                $username,
                $name,
                $email,
                $hashedPassword
            ]);

        } catch (\PDOException $e) {

            exit('Email already exists');

        }

        header('Location: /login');
        exit;
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $this->database->prepare("
            SELECT * FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (
            $user &&
            password_verify(
                $password,
                $user->password
            )
        ) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user->id;
            $_SESSION['role'] = $user->role;

            header('Location: /overview');
            exit;
        }

        exit('Invalid credentials');
    }

    /**
     * @throws Exception
     */
    public function overview(): Response
    {
        AuthMiddleware::handle();

        return $this->responseFactory->view('user/overview.html.twig',
        [
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
