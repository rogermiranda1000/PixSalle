<?php

namespace Salle\PixSalle\Model;

class Album
{
    private string $name;
    private int $id;
    private ?AlbumPhoto $photo;

    public function __construct(string $name, int $id) {
        $this->name = $name;
        $this->id = $id;
        $this->photo = null;
    }

    public function setPhoto(AlbumPhoto $photo) {
        $this->photo = $photo;
    }

    public function name(): string {
        return $this->name;
    }

    public function id(): int {
        return $this->id;
    }

    public function photo(): ?AlbumPhoto {
        return $this->photo;
    }
}