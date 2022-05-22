<?php

namespace Salle\PixSalle\Model;

class Album
{
    private string $name;
    private int $id;

    public function __construct(string $name, int $id) {
        $this->name = $name;
        $this->id = $id;
    }

    public function name(): string {
        return $this->name;
    }

    public function id(): int {
        return $this->id;
    }
}