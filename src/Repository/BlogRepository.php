<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Post;

interface BlogRepository {
    public function getAllPosts(): array;
    public function post(Post $post): void; // ID can be anything
    public function getPost(id $post_id): Post;
    public function updatePost(Post $post): void; // userID can be anything
    public function deletePost(id $post_id): void;
}
