<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Model\Post;

use Salle\PixSalle\Repository\BlogRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class BlogController
{
    private Twig $twig;
    private BlogRepository $blogRepository;

    public function __construct(Twig $twig, BlogRepository $blogRepository) {
        $this->twig = $twig;
        $this->blogRepository = $blogRepository;
    }

    public function getAllPosts(Request $request, Response $response): Response
    {
        //$test = new Post(2, "hello", "world", 1);
        //return $response->withJson($test->expose(), 200, JSON_PRETTY_PRINT);

        //$this->blogRepository->post($test);
        //return $response->withJson($this->blogRepository->getAllPosts(), 200, JSON_PRETTY_PRINT);

        //$this->blogRepository->deletePost(1);
        
        return $response->withJson($this->blogRepository->getPost(1)->expose(), 200, JSON_PRETTY_PRINT);

        //return $response->withJson($this->blogRepository->updatePost(new Post(2, "uwu", "world")), 200, JSON_PRETTY_PRINT);
    }
}
