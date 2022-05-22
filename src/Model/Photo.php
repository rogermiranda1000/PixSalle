<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Photo {
    private string $uuid_or_url;
    private ?string $extension; // null if url
    private string $author;
    
    public function __construct(string $uuid_or_url, string $extension, string $author) {
        $this->uuid_or_url = $uuid_or_url;
        $this->extension = $extension;
        $this->author = $author;
    }

    public function uuid() {
        return $this->uuid_or_url;
    }

    public function url(): ?string {
        if ($this->extension !== null) return null;
        return $this->uuid_or_url;
    }

    public function extension() {
        return $this->extension;
    }

    public function author() {
        return $this->author;
    }
}
