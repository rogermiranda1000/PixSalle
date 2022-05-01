<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Photo {
    private string $uuid;
    private string $extension;
    private string $author;
    
    public function __construct(string $uuid, string $extension, string $author) {
        $this->uuid = $uuid;
        $this->extension = $extension;
        $this->author = $author;
    }

    public function uuid() {
        return $this->uuid;
    }

    public function extension() {
        return $this->extension;
    }

    public function author() {
        return $this->author;
    }
}
