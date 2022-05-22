<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Model\Post;

use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Repository\BlogRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class BlogController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private BlogRepository $blogRepository;

    public function __construct(Twig $twig, UserRepository $userRepository, BlogRepository $blogRepository) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->blogRepository = $blogRepository;
    }

    public function getAllPosts(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'blog.twig',
            [
                'posts' => $this->blogRepository->getAllPosts()
            ]
        );
    }

    public function getPost(Request $request, Response $response, array $args): Response
    {
        $post = $this->blogRepository->getPost(intval($args['id']));
        $author = null;
        if ($post !== null) {
            $user = $this->userRepository->getUserById($post->user_id());
            $author = $user->username;
        }

        return $this->twig->render(
            $response,
            'post.twig',
            [
                'post' => $post,
                'author' => $author
            ]
        );
    }
}
