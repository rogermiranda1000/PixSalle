<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Model\Portfolio;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\AlbumRepository;
use Salle\PixSalle\Repository\PortfolioRepository;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

final class PortfolioController
{
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;
    private PortfolioRepository $portfolioRepository;
    private AlbumRepository $albumRepository;
    private Messages $flash;

    public function __construct(Twig $twig, UserRepository $userRepository, PortfolioRepository $portfolioRepository, AlbumRepository $albumRepository, Messages $flash) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->portfolioRepository = $portfolioRepository;
        $this->flash = $flash;
        $this->albumRepository = $albumRepository;
        $this->validator = new ValidatorService();
    }

    public function showPortfolioPage(Request $request, Response $response)
    {
       $portfolio = $this->portfolioRepository->getPortfolioByUserId($_SESSION['user_id']);

        // If the user doesn't have any portfolio
        if($portfolio === null)
        {
            return $this->twig->render($response, 'portfolio-create.twig', []);
        }

        $membership = $this->userRepository->getUserMembership($_SESSION['user_id']);

        // If the user doesn't have albums created
        $albums_array = $this->albumRepository->getAlbums($_SESSION['user_id']);
        if(empty($albums_array))
        {
            return $this->twig->render($response, 'portfolio-empty.twig', ['membership' => $membership]);
        }

        // If the user have albums created
        $albums = array();

        foreach ($albums_array as $album)
        {
            array_push($albums, [
                'id' => $album->id(),
                'name' => $album->name(),
                'img' => $album->photo()
            ]);
        }

        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                'membership' => $membership,
                'albums' => $albums
            ]
        );
    }

    public function postPortfolio(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if(array_key_exists('p_name', $data)){
            return $this->createPortfolio($request, $response);
        }

        return $this->createAlbum($request, $response);
    }

    public function createPortfolio(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];

        $errors['p_name'] = $this->validator->validateUsername($data['p_name']);

        if ($errors['p_name'] == '') {
            unset($errors['p_name']);
        }

        $savedPortfolio = $this->portfolioRepository->getPortfolioByUserId($_SESSION['user_id']);

        if ($savedPortfolio != null) {
            return $response->withHeader('Location', '/portfolio')->withStatus(302);
        }

        if (count($errors) == 0) {
            $portfolio = new Portfolio($data['p_name'], strval($_SESSION['user_id']));
            $this->portfolioRepository->createPortfolio($portfolio);
            return $response->withHeader('Location', '/portfolio')->withStatus(302);
        }

        return $this->twig->render(
            $response,
            'portfolio-create.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('portfolio')
            ]
        );
    }

    public function createAlbum(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];

        $errors['name'] = $this->validator->validateUsername($data['name']);

        if ($errors['name'] == '') {
            unset($errors['name']);
        }

        if (count($errors) == 0) {
            $portfolio = $this->portfolioRepository->getPortfolioNameByUserId($_SESSION['user_id']);
            $this->albumRepository->addAlbum($data['name'], strval($portfolio));
            return $response->withHeader('Location', '/portfolio')->withStatus(302);
        }

        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('portfolio')
            ]
        );
    }
}
