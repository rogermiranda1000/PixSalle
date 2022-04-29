<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Repository\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use DateTime;

final class MembershipController
{
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showMembershipForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $user = $this->userRepository->getUserById($_SESSION['user_id']);
        // we'll asume that the ID is always correct ($user != null)

        return $this->twig->render(
            $response,
            'membership.twig',
            [
                'formAction' => $routeParser->urlFor('membership'),
                'current' => $user->membership
            ]
        );
    }

    public function applyMembership(Request $request, Response $response): Response
    {
        // TODO
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];

        $errors['email'] = $this->validator->validateEmail($data['email']);
        $errors['password'] = $this->validator->validatePassword($data['password']);

        if ($errors['email'] == '') {
            unset($errors['email']);
        }
        if ($errors['password'] == '') {
            unset($errors['password']);
        }
        
        $savedUser = $this->userRepository->getUserByEmail($data['email']);
        if ($savedUser != null) {
            $errors['email'] = "User already exists!";
        }
        if (count($errors) == 0) {
            $user = new User($data['email'], md5($data['password']), new DateTime(), new DateTime());
            $this->userRepository->createUser($user);
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }
        return $this->twig->render(
            $response,
            'sign-up.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('signUp')
            ]
        );
    }
}
