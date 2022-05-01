<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\LandingPageController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\WalletController;
use Salle\PixSalle\Middleware\RequireLoginMiddleware;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', LandingPageController::class . ':showLandingPage')->setName('home');
    $app->get('/sign-in', UserSessionController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', UserSessionController::class . ':signIn');
    $app->get('/logout', UserSessionController::class . ':logout')->setName('logout');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUp');

    
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

    $app->get('/portfolio', PortfolioController::class . ':showPortfolioPage')
        ->setName('portfolio')
        ->add(RequireLoginMiddleware::class);
}
