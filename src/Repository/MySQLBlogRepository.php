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
        $query = "SELECT * FROM post";

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function post(Post $post): void {
        $query = "INSERT INTO post(title, content, user_id) VALUES(:title, :content, :user_id)";

        $statement = $this->databaseConnection->prepare($query);

        $title = $post->title();
        $content = $post->content();
        $user_id = $post->user_id();

        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getPost(id $post_id): Post {
        return null;
    }

    public function updatePost(Post $post): void {

    }

    public function deletePost(id $post_id): void {

    }
}
