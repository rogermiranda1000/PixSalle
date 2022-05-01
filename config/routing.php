<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\API\BlogAPIController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Middleware\RequireLoginMiddleware;
use Salle\PixSalle\Controller\ProfileController;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', UserSessionController::class . ':showSignInForm');
    $app->get('/sign-in', UserSessionController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', UserSessionController::class . ':signIn');
    $app->get('/logout', UserSessionController::class . ':logout')->setName('logout');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUp');

    $app->get('/profile', ProfileController::class . ':showProfileForm')
        ->setName('profile')
        ->add(RequireLoginMiddleware::class);
    $app->post('/profile', ProfileController::class . ':changeProfile')
        ->add(RequireLoginMiddleware::class);
    $app->get('/profile/changePassword', ProfileController::class . ':showChangePasswordForm')
        ->setName('changePassword')
        ->add(RequireLoginMiddleware::class);
    $app->post('/profile/changePassword', ProfileController::class . ':changePassword')
        ->add(RequireLoginMiddleware::class);


    $app->get('/user/membership', MembershipController::class . ':showMembershipForm')
        ->setName('membership')
        ->add(RequireLoginMiddleware::class);
    $app->post('/user/membership', MembershipController::class . ':applyMembership')
        ->add(RequireLoginMiddleware::class)
        ->add(RequireLoginMiddleware::class);

    $app->get('/explore', ExploreController::class . ':showImages')
        ->setName('explore')
        ->add(RequireLoginMiddleware::class);
}
