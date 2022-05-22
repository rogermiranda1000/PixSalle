<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Model\Photo;

use Salle\PixSalle\Repository\ImageRepository;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class ExploreController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;

    public function __construct(Twig $twig, UserRepository $userRepository, ImageRepository $imageRepository) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
    }

    public function showImages(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $photos = array();
        foreach ($this->userRepository->getPhotos() as $img) {
            array_push($photos, [
                'author' => $img->author(),
                'img' => ($img->url() !== null) ? $img->url() : $this->imageRepository->getPhoto($img->uuid(), $img->extension())
            ]);
        }

        return $this->twig->render(
            $response,
            'explore.twig',
            [
                'photos' => $photos
            ]
        );
    }
}
