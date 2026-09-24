<?php

namespace App\Controllers;

use App\Repositories\ProfileRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Session;

class ContactController
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private ProfileRepositoryInterface $profiles,
        private Session $session
    ) {
    }

    public function index(Request $request): Response
    {
        $profile = $this->profiles->get();

        return $this->responseFactory->view('contact.html.twig', [
            'active'  => 'contact',
            'profile' => $profile,
        ]);
    }

    public function send(Request $request): Response
    {
        $token = $request->get('csrf_token') ?? '';
        if (!$this->session->validateCsrfToken($token)) {
            return $this->responseFactory
                ->createToast('error', 'Ongeldig verzoek. Probeer opnieuw.')
                ->redirect('/contact');
        }

        // Honeypot: real visitors never fill this hidden field, bots usually do.
        if (($request->get('website') ?? '') !== '') {
            return $this->responseFactory->redirect('/contact');
        }

        $name    = htmlspecialchars(trim($request->get('name') ?? ''));
        $email   = (string) filter_var(trim($request->get('email') ?? ''), FILTER_SANITIZE_EMAIL);
        $message = htmlspecialchars(trim($request->get('message') ?? ''));

        if (!$name || !$message) {
            return $this->responseFactory
                ->createToast('error', 'Vul alle velden in.')
                ->redirect('/contact');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->responseFactory
                ->createToast('error', 'Voer een geldig e-mailadres in.')
                ->redirect('/contact');
        }

        if (!$this->sendMail($name, $email, $message)) {
            return $this->responseFactory
                ->createToast('error', 'Er is iets misgegaan. Probeer het later opnieuw.')
                ->redirect('/contact');
        }

        return $this->responseFactory
            ->createToast('success', 'Bericht verzonden! Je krijgt zo snel mogelijk antwoord.')
            ->redirect('/contact');
    }

    private function sendMail(string $name, string $email, string $message): bool
    {
        $to      = $_ENV['CONTACT_EMAIL'] ?? 'elmarvloenhout@gmail.com';
        $host    = parse_url((string)($_ENV['APP_URL'] ?? ''), PHP_URL_HOST) ?: 'localhost';
        $from    = 'noreply@' . $host;
        $subject = 'Nieuw contactbericht van ' . $name;

        $headers = [
            'From: ' . $from,
            'Reply-To: ' . $email,
            'Content-Type: text/plain; charset=UTF-8',
        ];

        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
}
