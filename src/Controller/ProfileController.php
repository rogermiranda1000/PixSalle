<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\ImageRepository;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class ProfileController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;
    private ValidatorService $validator;

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
        $this->validator = new ValidatorService();
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
        $uploadedFiles = $request->getUploadedFiles();
        $errors = [];
        $data = $request->getParsedBody();

        if (count($uploadedFiles['files']) !== 1) {
            $errors['file'] = 'Only one file is allowed';
        }

        $uploadedFile = $uploadedFiles['files'][0];
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
        $errors['username'] = $this->validator->validateUsername($data['username']);
        if ($errors['username'] == '') {
            unset($errors['username']);
        }
        $errors['phone'] = $this->validator->validatePhone($data['phone']);
        if ($errors['phone'] == '') {
            unset($errors['phone']);
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
        $image = $this->imageRepository->getPhoto($uuid, $format);


        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formAction' => $routeParser->urlFor('profile'),
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'profilePicture' => $image
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

    public function changePassword(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $errors = [];
        $data = $request->getParsedBody();

        $errors['oldPassword'] = $this->validator->validatePassword($data['oldPassword']);
        if ($errors['oldPassword'] == '') {
            unset($errors['oldPassword']);
        }
        $errors['newPassword'] = $this->validator->validatePassword($data['newPassword']);
        if ($errors['newPassword'] == '') {
            unset($errors['newPassword']);
        }
        if (md5($data['oldPassword']) != $this->userRepository->getUserById($_SESSION['user_id'])->password) {
            $errors['confirmPassword'] = 'This is not your actual password';
        }
        if ($data['newPassword'] != $data['confirmPassword']) {
            $errors['confirmPassword'] = "Repeat password must match new password";
        }
        if (count($errors) > 0) {
            return $this->twig->render(
                $response,
                'change-password.twig',
                [
                    'formAction' => $routeParser->urlFor('changePassword'),
                    'formErrors' => $errors
                ]
            );
        }
        // Update password in ddbb
        $this->userRepository->changePassword($_SESSION['user_id'], md5($data['newPassword']));
        return $this->twig->render(
            $response,
            'change-password.twig',
            [
                'formAction' => $routeParser->urlFor('changePassword'),
                'done' => 'Password changed!'
            ]
        );

    }

}