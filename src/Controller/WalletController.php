<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Repository\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

final class WalletController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private Messages $flash;

    public function __construct(Twig $twig, UserRepository $userRepository, Messages $flash) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->flash = $flash;
    }

    public function showWalletForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $user = $this->userRepository->getUserById($_SESSION['user_id']);
        // we'll asume that the ID is always correct ($user != null)

        $messages = $this->flash->getMessages();
        $errors = $messages['errors'] ?? [];

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'errors' => $errors,

                'formAction' => $routeParser->urlFor('wallet'),
                'money' => $user->wallet
            ]
        );
    }

    public function addToWallet(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!is_numeric($data['amount'])) {
            $this->flash->addMessageNow('errors', 'Please enter a number.');
            return $this->showWalletForm($request, $response);
        }

        $amount = floatval($data['amount']);
        if ($amount <= 0) {
            $this->flash->addMessageNow('errors', 'Please enter a positive number.');
            return $this->showWalletForm($request, $response);
        }

        // TODO add money
        return $this->showWalletForm($request, $response);
    }
}
