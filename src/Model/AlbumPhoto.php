<?php

namespace Salle\PixSalle\Model;

class AlbumPhoto
{
    private string $url;
    private int $id;

    public function __construct(string $url, int $id) {
        $this->url = $url;
        $this->id = $id;
    }

    public function url(): string {
        return $this->url;
    }

    public function id(): int {
        return $this->id;
    }
}