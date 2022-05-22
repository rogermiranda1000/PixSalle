<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;


interface AlbumRepository {
    public function getAlbumPhotos(int $album);
    public function getAlbumName(int $album): string;
    public function addPhoto(int $album, string $url);
    public function deletePhoto(int $album, int $photo);
    public function deleteAlbum(int $album);
    public function addAlbum(string $name, string $portfolio);
}
