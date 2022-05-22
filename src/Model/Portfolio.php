<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Portfolio {
    private string $name;
    private string $user_id;
    
    public function __construct(string $name, string $user_id) {
    }

    public function name() {
        return $this->name;
    }

    public function user_id() {
        return $this->user_id;
    }
}
