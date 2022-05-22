<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Post;

interface BlogRepository {
    public function getAllPosts(): array;
    public function post(Post $post): ?Post; // ID can be anything; if null returned means author FK violation
    public function getPost(int $post_id): ?Post;
    public function updatePost(Post $post): bool; // userID can be anything
    public function deletePost(int $post_id): bool;
}
