<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Salle\PixSalle\Model\Post;

use Salle\PixSalle\Repository\BlogRepository;
use Slim\Routing\RouteContext;

final class BlogApiController
{
    private BlogRepository $blogRepository;

    public function __construct(BlogRepository $blogRepository) {
        $this->blogRepository = $blogRepository;
    }

    public function getAllPosts(Request $request, Response $response): Response
    {
        return $response->withJson($this->blogRepository->getAllPosts(), 200, JSON_PRETTY_PRINT);
    }

    public function insertPost(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        $error = [];
        if (!isset($data['userId']) || empty($data['userId']) || !isset($data['title']) || empty($data['title']) || !isset($data['content']) || empty($data['content'])) {
            $error['message'] = "'title' and/or 'content' and/or 'userId' key missing";
            return $response->withJson($error, 400, JSON_PRETTY_PRINT);
        }

        $user = intval($data['userId']);
        $post = $this->blogRepository->post(new Post(-1, $data['title'], $data['content'], $user));
        if ($post === null) {
            $error['message'] = "the user $user doesn't exists";
            return $response->withJson($error, 404, JSON_PRETTY_PRINT);
        }

        return $response->withJson($post->expose(), 201, JSON_PRETTY_PRINT);
    }

    public function getPost(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $post = $this->blogRepository->getPost($id);
        if ($post === null) {
            $error = [];
            $error['message'] = "Blog entry with id $id does not exist";
            return $response->withJson($error, 404, JSON_PRETTY_PRINT);
        }

        return $response->withJson($post->expose(), 200, JSON_PRETTY_PRINT);
    }

    public function updatePost(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $error = [];
        if (!isset($data['title']) || empty($data['title']) || !isset($data['content']) || empty($data['content'])) {
            $error['message'] = "'title' and/or 'content' key missing";
            return $response->withJson($error, 400, JSON_PRETTY_PRINT);
        }

        $id = intval($args['id']);
        $changed = $this->blogRepository->updatePost(new Post($id, $data['title'], $data['content']));
        if (!$changed) {
            $error['message'] = "Blog entry with id $id does not exist";
            return $response->withJson($error, 404, JSON_PRETTY_PRINT);
        }

        // reload the post
        return $response->withJson($this->blogRepository->getPost($id)->expose(), 200, JSON_PRETTY_PRINT);
    }

    public function deletePost(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $changed = $this->blogRepository->deletePost($id);

        $data = [];
        if (!$changed) {
            $data['message'] = "Blog entry with id $id does not exist";
            return $response->withJson($data, 404, JSON_PRETTY_PRINT);
        }

        $data['message'] = "Blog entry with id $id was successfully deleted";
        return $response->withJson($data, 200, JSON_PRETTY_PRINT);
    }
}
