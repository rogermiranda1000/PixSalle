<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Model\Portfolio;
use Salle\PixSalle\Repository\AlbumRepository;
use Salle\PixSalle\Repository\PortfolioRepository;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

final class PortfolioController
{
    private Twig $twig;
    private ValidatorService $validator;
    private PortfolioRepository $portfolioRepository;
    private AlbumRepository $albumRepository;
    private Messages $flash;

    public function __construct(Twig $twig, PortfolioRepository $portfolioRepository, AlbumRepository $albumRepository, Messages $flash) {
        $this->twig = $twig;
        $this->portfolioRepository = $portfolioRepository;
        $this->flash = $flash;
        $this->albumRepository = $albumRepository;
        $this->validator = new ValidatorService();
    }

    public function showPortfolioPage(Request $request, Response $response)
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $portfolio = $this->portfolioRepository->getPortfolioByUserId($_SESSION['user_id']);

        // If the user doesn't have any portfolio
        if($portfolio === null)
        {
            return $this->twig->render($response, 'portfolio-create.twig', []);
        }

        // If the user have a portfolio
        $photos = array();

        /*
         * foreach ($this->albumRepository->FunctionCarles as $album) {
            array_push($photos, [
                'name' => $album->name(),
                'img' => $this->imageRepository->getPhoto($img->uuid(), $img->extension())
            ]);
        }
        */

        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                'photos' => $photos
            ]
        );
    }

    public function createPortfolio(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];

        $errors['name'] = $this->validator->validateUsername($data['name']);

        if ($errors['name'] == '') {
            unset($errors['name']);
        }

        $savedPortfolio = $this->portfolioRepository->getPortfolioByUserId($_SESSION['user_id']);

        if ($savedPortfolio != null) {
            return $response->withHeader('Location', '/portfolio')->withStatus(302);
        }

        if (count($errors) == 0) {
            $portfolio = new Portfolio($data['name'], strval($_SESSION['user_id']));
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
}
