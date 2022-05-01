<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Photo;

interface ImageRepository {
    public function getPhoto(string $uuid, string $path): string;
    public function savePhoto(string $photo): string; // TODO arguments?
}
