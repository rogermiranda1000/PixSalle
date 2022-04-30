<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Model\Photo;

use Salle\PixSalle\Repository\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class ExploreController
{
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showImages(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $user = $this->userRepository->getUserById($_SESSION['user_id']);
        // we'll asume that the ID is always correct ($user != null)

        return $this->twig->render(
            $response,
            'explore.twig',
            [
                
            ]
        );
    }
}
