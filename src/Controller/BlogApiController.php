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
        $json = $app->request->getBody();
        $data = json_decode($json, true);

        if (!isset($data['userId']) || empty($data['userId']) || !isset($data['title']) || empty($data['title']) || !isset($data['content']) || empty($data['content'])) {
            $error = [];
            $error['message'] = "'title' and/or 'content' and/or 'userId' key missing";
            return $response->withJson($error, 400, JSON_PRETTY_PRINT);
        }

        $post = $this->blogRepository->post(new Post(-1, $data['title'], $data['content'], $data['userId'])));
        return $response->withJson($post->expose(), 200, JSON_PRETTY_PRINT);
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

    public function updatePost(Request $request, Response $response): Response
    {
        $json = $app->request->getBody();
        $data = json_decode($json, true);

        $error = [];
        if (!isset($data['title']) || empty($data['title']) || !isset($data['content']) || empty($data['content'])) {
            $error['message'] = "The title and/or content cannot be empty";
            return $response->withJson($error, 400, JSON_PRETTY_PRINT);
        }

        $id = intval($args['id']);
        $changed = $response->withJson($this->blogRepository->updatePost(new Post($id, $data['title'], $data['content'])), 200, JSON_PRETTY_PRINT);
        if (!$changed) {
            $error['message'] = "Blog entry with id $id does not exist";
            return $response->withJson($error, 404, JSON_PRETTY_PRINT);
        }

        // reload the post
        return $response->withJson($this->blogRepository->getPost($id)->expose(), 200, JSON_PRETTY_PRINT);
    }

    public function deletePost(Request $request, Response $response): Response
    {
        $id = intval($args['id']);
        $changed = $this->blogRepository->deletePost($id);

        $data = [];
        if (!$changed) {
            $data['message'] = "Blog entry with id $id does not exist";
            return $response->withJson($data, 404, JSON_PRETTY_PRINT);
        }
        return $response->withJson($data, 200, JSON_PRETTY_PRINT); // TODO
    }
}
