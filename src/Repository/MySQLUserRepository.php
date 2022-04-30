<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Model\Photo;
use Salle\PixSalle\Repository\UserRepository;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function setUserMembership(int $id, string $new_membership): void {
        $query = "UPDATE users SET membership = :membership WHERE id = :id";

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('membership', $new_membership, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getPhotos() {
        $query = <<<'QUERY'
        SELECT photos.uuid, photos.extension, profile.username AS profile_photo_author, uploader.username AS upload_photo_author
        FROM photos LEFT JOIN users AS profile ON profile.profile_picture = photos.uuid -- profile pictures

           -- upload pictures
           LEFT JOIN albumphoto ON photos.uuid = albumphoto.photo_id
           LEFT JOIN albums ON (albumphoto.album_name = albums.name
                                  AND albumphoto.portfolio_name = albums.portfolio_name)
           LEFT JOIN portfolios ON portfolios.name = albums.portfolio_name
           LEFT JOIN users AS uploader ON uploader.id = portfolios.user_id;
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $stmt->bind_result($photo_uuid, $photo_extension, $profile_photo_author, $upload_photo_author);

        $results = array();
        while ($stmt->fetch()) {
            array_push($results, new Photo($photo_uuid . '.' . $photo_extension, ($profile_photo_author === null) ? $upload_photo_author : $profile_photo_author));
        }
        return $results;
    }

    public function createUser(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(email, password, createdAt, updatedAt)
        VALUES(:email, :password, :createdAt, :updatedAt)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserById(int $id) {
        $query = "SELECT * FROM users WHERE id = :id";

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getUserByEmail(string $email)
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }
}
