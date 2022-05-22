<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;


interface AlbumRepository {
    public function getAlbumPhotos(int $album);
    public function addPhoto(int $album, string $portfolio, string $url);
    public function deletePhoto(int $album, string $portfolio, int $photo);
    public function addAlbum(string $name, string $portfolio);
}
