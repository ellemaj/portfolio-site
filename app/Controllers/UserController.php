<?php

namespace App\Controllers;

use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\CourseRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use App\Models\User;
use Framework\Session;

class UserController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserRepositoryInterface $users,
        private Session $session,
        private PostRepositoryInterface $posts,
        private CourseRepositoryInterface $courses,
        private ProjectRepositoryInterface $projects,
    ) {
    }

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
        $firstName       = htmlspecialchars(trim($request->get('firstName') ?? ''));
        $lastName        = htmlspecialchars(trim($request->get('lastName') ?? ''));
        $email           = (string) filter_var(trim($request->get('email') ?? ''), FILTER_SANITIZE_EMAIL);
        $password        = $request->get('password') ?? '';
        $passwordConfirm = $request->get('password_confirm') ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->responseFactory
                ->createToast('error', 'Voer een geldig e-mailadres in.')
                ->redirect('/register');
        }

        if (strlen($password) < 8) {
            return $this->responseFactory
                ->createToast('error', 'Wachtwoord moet minimaal 8 tekens zijn.')
                ->redirect('/register');
        }

        if ($password !== $passwordConfirm) {
            return $this->responseFactory
                ->createToast('error', 'Wachtwoorden komen niet overeen.')
                ->redirect('/register');
        }

        if (!$firstName || !$lastName) {
            return $this->responseFactory
                ->createToast('error', 'Vul alle velden in.')
                ->redirect('/register');
        }

        if ($this->users->findByEmail($email)) {
            return $this->responseFactory
                ->createToast('error', 'Er bestaat al een gebruiker met dit emailadres.')
                ->redirect('/register');
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
            ->redirect('/register');
        }

        return $this->responseFactory
        ->createToast('success', 'Account aangemaakt! Je kunt nu inloggen.')
        ->redirect('/login');
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
        $courses        = $this->courses->findAll();
        $earned         = array_sum(array_map(
            fn($c) => ($c->grade !== null && $c->grade >= 5.5) ? $c->ec : 0,
            $courses
        ));
        $total          = array_sum(array_map(fn($c) => $c->ec, $courses));

        return $this->responseFactory->view('user/overview.html.twig', [
            'active'      => 'overview',
            'postCount'   => count($publishedPosts),
            'recentPosts' => array_slice($publishedPosts, 0, 3),
            'earned'      => $earned,
            'total'       => $total,
            'ecPercent'   => $total > 0 ? round($earned / $total * 100) : 0,
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
