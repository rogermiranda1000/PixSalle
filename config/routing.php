<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\API\BlogAPIController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', UserSessionController::class . ':showSignInForm')->setName('signIn');
    $app->get('/sign-in', UserSessionController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', UserSessionController::class . ':signIn');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUp');

    $app->get('/profile', SignUpController::class . ':showProfileForm')->setName('profile');
    $app->post('/profile', SignUpController::class . ':changeProfile');
    $app->get('/profile/changePassword', SignUpController::class . ':showChangePasswordForm')->setName('changePassword');
    $app->post('/profile/changePassword', SignUpController::class . ':changePassword');
}
