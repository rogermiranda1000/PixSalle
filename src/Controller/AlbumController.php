<?php


namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\ImageRepository;
use Salle\PixSalle\Repository\AlbumRepository;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class AlbumController
{
    private Twig $twig;
    private AlbumRepository $albumRepository;
    private Messages $flash;

    public function __construct(Twig $twig, AlbumRepository $userRepository, Messages $flash) {
        $this->twig = $twig;
        $this->albumRepository = $userRepository;
        $this->flash = $flash;
    }

    public function showAlbum(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $user = $this->albumRepository->getUserById($_SESSION['user_id']);
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formAction' => $routeParser->urlFor('profile'),
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone
            ]
        );
    }

    public function deleteAlbum(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $user = $this->userRepository->getUserById($_SESSION['user_id']);
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formAction' => $routeParser->urlFor('profile'),
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone
            ]
        );
    }

    public function createAlbum(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $user = $this->userRepository->getUserById($_SESSION['user_id']);
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formAction' => $routeParser->urlFor('profile'),
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone
            ]
        );
    }
}