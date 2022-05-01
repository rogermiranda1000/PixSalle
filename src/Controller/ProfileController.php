<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\ImageRepository;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class ProfileController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;

    private const ALLOWED_EXTENSIONS = ['jpg', 'png'];
    private const MAXSIZE = 500;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository,
        ImageRepository $imageRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
    }
    public function showProfileForm(Request $request, Response $response): Response {
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

    public function changeProfile(Request $request, Response $response): Response {
        $uploadedFile = $request->getUploadedFiles();
        $errors = [];

        $fileName = $uploadedFile->getClientFilename();
        $fileInfo = pathinfo($fileName);
        $format = $fileInfo['extension'];
        $size = getimagesize($fileName);
        if (!$this->isValidFormat($format)) {
            $errors['image'] = 'Only png and jpg images are allowed';
        }
        else if( !$this->isValidDimensions($size)) {
            $errors['image'] = 'The image should be (500 x 500) or less';
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $user = $this->userRepository->getUserById($_SESSION['user_id']);
        if (count($errors) > 0) {
            return $this->twig->render(
                $response,
                'profile.twig',
                [
                    'formAction' => $routeParser->urlFor('profile'),
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'errors' => $errors
                ]
            );
        }
        $uuid = $this->imageRepository->savePhoto($fileName, $format);
        $this->userRepository->createPhoto($_SESSION['user_id'], $uuid, $format);


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

    private function isValidFormat(string $extension): bool {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    private function isValidDimensions($dimensions): bool {
        return ($dimensions->width <= self::MAXSIZE && $dimensions->height <= self::MAXSIZE);
    }

    public function showChangePasswordForm(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render(
            $response,
            'change-password.twig',
            [
                'formAction' => $routeParser->urlFor('changePassword')
            ]
        );
    }

}