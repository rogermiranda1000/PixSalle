<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class LandingPageController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function showLandingPage(Request $request, Response $response)
    {
        if (empty($_SESSION['user_id'])) {
            return $this->twig->render($response, 'landing-page.twig', []);
        }
        return $this->twig->render($response, 'landing-page-in.twig', []);
    }
}