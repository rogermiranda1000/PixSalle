<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\AlbumPhoto;
use Salle\PixSalle\Repository\AlbumRepository;

final class MySQLAlbumRepository implements AlbumRepository
{
    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function getAlbumPhotos(int $album)
    {
        $query = <<<'QUERY'
        SELECT photo.url AS url, photo.id AS id
        FROM albumPhotos AS photo WHERE photo.album_id = :album
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album', $album, PDO::PARAM_INT);

        $statement->execute();

        $results = array();
        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            array_push($results, new AlbumPhoto($row->url, $row->id));
        }
        return $results;
    }

    public function addPhoto(int $album, string $portfolio, string $url)
    {
        $query = <<<'QUERY'
        INSERT INTO albumPhotos(album_id, portfolio_name, url)
        VALUES(:album, :portfolio, :url)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album', $name, PDO::PARAM_STR);
        $statement->bindParam('portfolio', $portfolio, PDO::PARAM_STR);
        $statement->bindParam('url', $url, PDO::PARAM_STR);

        $statement->execute();
    }

    public function deletePhoto(int $album, string $portfolio, int $photo)
    {
        $query = <<<'QUERY'
        DELETE FROM albumPhotos AS photo WHERE photo.album_id = :album AND photo.portfolio_name = :portfolio AND  photo.photo_id = :photo;
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album', $album, PDO::PARAM_INT);
        $statement->bindParam('portfolio_name', $portfolio, PDO::PARAM_STR);
        $statement->bindParam('photo', $photo, PDO::PARAM_INT);

        $statement->execute();
    }

    public function addAlbum(string $name, string $portfolio)
    {
        $query = <<<'QUERY'
        INSERT INTO albums(name, portfolio_name)
        VALUES(:album_name, :portfolio_name)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album_name', $name, PDO::PARAM_STR);
        $statement->bindParam('portfolio_name', $portfolio, PDO::PARAM_STR);

        $statement->execute();
    }
}
