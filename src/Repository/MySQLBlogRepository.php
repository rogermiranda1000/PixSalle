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

    private function getLastAddedPost(): Post {
        $query = "SELECT * FROM post WHERE id = LAST_INSERT_ID()";

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            return new Post(intval($row->id), $row->title, $row->content, intval($row->user_id));
        }
        return null; // shouldn't get here
    }

    public function post(Post $post): ?Post {
        $query = "INSERT INTO post(title, content, user_id) VALUES(:title, :content, :user_id)";

        $statement = $this->databaseConnection->prepare($query);

        $title = $post->title();
        $content = $post->content();
        $user_id = $post->user_id();

        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();

        // TODO FK violation

        return $this->getLastAddedPost();
    }

    public function getPost(int $post_id): ?Post {
        $query = "SELECT * FROM post WHERE id = :id";

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $post_id, PDO::PARAM_INT);

        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            return new Post(intval($row->id), $row->title, $row->content, intval($row->user_id));
        }
        return null;
    }

    public function updatePost(Post $post): bool {
        $query = "UPDATE post SET title = :title, content = :content WHERE id = :id";

        $statement = $this->databaseConnection->prepare($query);
        
        $id = $post->id();
        $title = $post->title();
        $content = $post->content();
        
        $statement->bindParam('id', $id, PDO::PARAM_INT);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);

        $statement->execute();
        $count = $statement->rowCount();
        return ($count > 0);
    }

    public function deletePost(int $post_id): bool {
        $query = "DELETE FROM post WHERE id = :id";

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $post_id, PDO::PARAM_INT);

        $statement->execute();
        $count = $statement->rowCount();
        return ($count > 0);
    }
}
