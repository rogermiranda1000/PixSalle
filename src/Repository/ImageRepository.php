<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Photo;

interface ImageRepository {
    public function getPhoto(string $uuid, string $extension): string;
    public function savePhoto($photo): string;
    public function getPhotoSize(string $uuid, string $extension): array;
    public function removePhoto(string $uuid, string $extension): void;
}
