<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Portfolio {
    private string $name;
    private string $user_id;
    
    public function __construct(string $name, string $user_id) {
        $this->name = $name;
        $this->user_id = $user_id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function user_id(): string
    {
        return $this->user_id;
    }
}
