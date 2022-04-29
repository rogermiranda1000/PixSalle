<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class ProfileController

{
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }
    public function showProfileForm(Request $request, Response $response): Response {
        return $this->twig->render($response, 'profile.twig');
    }
    public function showChangePasswordForm(Request $request, Response $response): Response {
        return $this->twig->render($response, 'change-password.twig');
    }

}