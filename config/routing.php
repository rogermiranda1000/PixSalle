<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\LandingPageController;
use Salle\PixSalle\Controller\BlogController;
use Salle\PixSalle\Controller\BlogApiController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\WalletController;
use Salle\PixSalle\Middleware\RequireLoginMiddleware;
use Salle\PixSalle\Middleware\IntegerIdCheckerMiddleware;
use Salle\PixSalle\Controller\ProfileController;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', LandingPageController::class . ':showLandingPage')->setName('home');
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
        ->add(RequireLoginMiddleware::class);

    $app->get('/explore', ExploreController::class . ':showImages')
        ->setName('explore')
        ->add(RequireLoginMiddleware::class);
        
    $app->get('/user/wallet', WalletController::class . ':showWalletForm')
        ->setName('wallet')
        ->add(RequireLoginMiddleware::class);
    $app->post('/user/wallet', WalletController::class . ':addToWallet')
        ->add(RequireLoginMiddleware::class);
        
    $app->get('/api/blog', BlogApiController::class . ':getAllPosts');
    $app->post('/api/blog', BlogApiController::class . ':insertPost');
    $app->get('/api/blog/{id}', BlogApiController::class . ':getPost')
        ->add(IntegerIdCheckerMiddleware::class);
    $app->put('/api/blog/{id}', BlogApiController::class . ':updatePost')
        ->add(IntegerIdCheckerMiddleware::class);
    $app->delete('/api/blog/{id}', BlogApiController::class . ':deletePost')
        ->add(IntegerIdCheckerMiddleware::class);
       
    $app->get('/blog', BlogController::class . ':getAllPosts');
    $app->get('/blog/{id}', BlogController::class . ':getPost')
        ->add(IntegerIdCheckerMiddleware::class);
}
