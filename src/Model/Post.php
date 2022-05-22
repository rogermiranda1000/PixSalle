<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Post {
    private int $id;
    private string $title;
    private string $content;
    private int $user_id;
    
    public function __construct(int $id, string $title, string $content, int $user_id = -1) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->user_id = $user_id;
    }

    public function id() {
        return $this->id;
    }

    public function title() {
        return $this->title;
    }

    public function content() {
        return $this->content;
    }

    public function user_id() {
        return $this->user_id;
    }

    public function expose() {
        return get_object_vars($this);
    }
}
