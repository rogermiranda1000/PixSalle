<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Ramsey\Uuid\Uuid;
use Salle\PixSalle\Model\Album;
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
        SELECT photo.url AS url, photo.photo_id AS id
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

    public function getAlbumName(int $album): string
    {
        $query = <<<'QUERY'
        SELECT albums.name AS  name
        FROM albums WHERE albums.id = :album
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('album', $album, PDO::PARAM_INT);
        $statement->execute();
        if ($result = $statement->fetch(PDO::FETCH_OBJ)) {
            return $result->name;
        }
        return '';
    }

    private function getPortfolioName(int $album) {
        $query = <<<'QUERY'
        SELECT albums.portfolio_name AS portfolio
        FROM albums WHERE albums.id = :album
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('album', $album, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ)->portfolio;
    }

    public function addPhoto(int $album, string $url)
    {
        $portfolio = $this->getPortfolioName($album);

        $query = <<<'QUERY'
        INSERT INTO albumPhotos(album_id, portfolio_name, url, photo_id)
        VALUES(:album, :portfolio, :url, :id)
        QUERY;

        $id = Uuid::uuid4()->toString();
        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album', $album, PDO::PARAM_STR);
        $statement->bindParam('portfolio', $portfolio, PDO::PARAM_STR);
        $statement->bindParam('url', $url, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
        return $id;
    }

    public function deletePhoto(int $album, int $photo)
    {
        $portfolio_name = $this->getPortfolioName($album);

        $query = <<<'QUERY'
        DELETE FROM albumPhotos AS photo WHERE photo.album_id = :album AND photo.portfolio_name = :portfolio AND  photo.photo_id = :photo;
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album', $album, PDO::PARAM_INT);
        $statement->bindParam('portfolio_name', $portfolio_name, PDO::PARAM_STR);
        $statement->bindParam('photo', $photo, PDO::PARAM_INT);

        $statement->execute();
    }

    public function deleteAlbum(int $album) {
        // Delete photos from album
        $query = <<<'QUERY'
        DELETE FROM albumPhotos WHERE albumPhotos.album_id = :album
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('album', $album, PDO::PARAM_INT);
        $statement->execute();

        // Delete album
        $query = <<<'QUERY'
        DELETE FROM albums WHERE albums.id = :album
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('album', $album, PDO::PARAM_INT);
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

    public function getAlbums(int $user) {
        $query = <<<'QUERY'
        SELECT albums.id AS album_id, albums.name AS album_name, albumPhotos.url, albumPhotos.photo_id
        FROM portfolios JOIN albums ON portfolios.name = albums.portfolio_name
            LEFT JOIN albumPhotos ON albums.id = albumPhotos.album_id
        WHERE portfolios.user_id = :user
        AND (albumPhotos.photo_id IS NULL OR albumPhotos.photo_id IN (SELECT MAX(photo_id) FROM albumPhotos GROUP BY album_id));
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('user', $user, PDO::PARAM_INT);
        $statement->execute();

        $results = array();
        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $album = new Album($row->album_name, intval($row->album_id));

            if ($row->url !== null) {
                $photo = new AlbumPhoto($row->url, $row->photo_id);
                $album->setPhoto($photo);
            }

            array_push($results, $album);
        }
        return $results;
    }
}
