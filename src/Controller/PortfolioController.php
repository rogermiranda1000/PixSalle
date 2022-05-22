<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Repository\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

final class PortfolioController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private Messages $flash;

    public function __construct(Twig $twig, UserRepository $userRepository, Messages $flash) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->flash = $flash;
    }

    public function showPortfolioPage(Request $request, Response $response)
    {
        return $this->twig->render($response, 'portfolio.twig', []);
    }

    public function addToPortfolio(Request $request, Response $response): Response
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

        $this->userRepository->modifyWallet($_SESSION['user_id'], $amount);

        return $this->showWalletForm($request, $response);
    }
}
