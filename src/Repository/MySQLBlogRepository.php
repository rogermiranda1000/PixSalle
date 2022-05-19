<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\Post;
use Salle\PixSalle\Repository\BlogRepository;

final class MySQLBlogRepository implements BlogRepository
{
    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function getAllPosts(): array {
        return null;
    }

    public function post(Post $post): void {

    }

    public function getPost(id $post_id): Post {
        return null;
    }

    public function updatePost(Post $post): void {

    }

    public function deletePost(id $post_id): void {

    }
}
