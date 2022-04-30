<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Photo {
    private string $path; // UUID + extension
    private string $author;
    
    public function __construct(string $uuid, string $extension, string $author) {
        $this->path = $uuid . '.' . $extension;
        $this->author = $author;
    }

    public function path() {
        return $this->path;
    }

    public function author() {
        return $this->author;
    }
}
