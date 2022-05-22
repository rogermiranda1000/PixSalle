<?php

namespace Salle\PixSalle\Model;

class AlbumPhoto
{
    private string $url;
    private string $id;

    public function __construct(string $url, string $id) {
        $this->url = $url;
        $this->id = $id;
    }

    public function url(): string {
        return $this->url;
    }

    public function id(): string {
        return $this->id;
    }
}