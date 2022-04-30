<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Repository\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

use DateTime;

final class MembershipController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private Messages $flash;

    public function __construct(Twig $twig, UserRepository $userRepository, Messages $flash) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->flash = $flash;
    }

    public function showMembershipForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $user = $this->userRepository->getUserById($_SESSION['user_id']);
        // we'll asume that the ID is always correct ($user != null)

        $messages = $this->flash->getMessages();
        $errors = $messages['errors'] ?? [];

        return $this->twig->render(
            $response,
            'membership.twig',
            [
                'errors' => $errors,

                'formAction' => $routeParser->urlFor('membership'),
                'current' => $user->membership
            ]
        );
    }

    public function applyMembership(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if ($data['membership'] !== "active" && $data['membership'] !== "cool") {
            $this->flash->addMessageNow('errors', 'Membership can only be \'active\' or \'cool\'!');
            return $this->showMembershipForm($request, $response);
        }

        $this->userRepository->setUserMembership($_SESSION['user_id'], $data['membership']);
        return $this->showMembershipForm($request, $response);
    }
}
