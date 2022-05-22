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

    public function modifyWallet(int $id, float $amount): void {
        $query = "UPDATE users SET wallet = GREATEST(wallet + (:amount / 100), 0)  WHERE id = :id";

        $amount *= 100;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('amount', $amount, PDO::PARAM_INT);
        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function setUserMembership(int $id, string $new_membership): void {
        $query = "UPDATE users SET membership = :membership WHERE id = :id";

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('membership', $new_membership, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getNextUsername(): string {
        $query = "SELECT CONCAT('user', IFNULL(AUTO_INCREMENT, '1')) AS next_username FROM information_schema.TABLES WHERE (TABLE_SCHEMA = (SELECT DATABASE()) AND TABLE_NAME = 'users')";

        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_OBJ);
        // we'll asume it's always OK
        return $row->next_username;
    }

    public function getPhotos() {
        // profile pictures
        $query =   "SELECT photos.uuid, photos.extension, profile.username
                    FROM photos LEFT JOIN users AS profile ON profile.profile_picture = photos.uuid";

        // uploaded pictures
        $query2 =  "SELECT albumPhotos.url, users.username
                    FROM albumPhotos JOIN portfolios ON albumPhotos.portfolio_name = portfolios.name
                        JOIN users ON portfolios.user_id = users.id;";

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $results = array();
        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            array_push($results, new Photo($row->uuid, $row->extension, $row->username));
        }
        
        $statement = $this->databaseConnection->prepare($query2);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            array_push($results, new Photo($row->url, $row->username));
        }

        return $results;
    }

    public function createUser(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(email, username, password, createdAt, updatedAt, phone)
        VALUES(:email, :username, :password, :createdAt, :updatedAt, :phone)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $username = ($user->username() === null) ? $this->getNextUsername() : $user->username();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $phone = $user->phone();

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);

        $statement->execute();
    }

    public function modifyUserBasic($id, $username, $phone): void
    {
        $query = <<<'QUERY'
        UPDATE users SET
            username = :username, phone = :phone
        WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function createPhoto($idUser, $uuid, $extension): void {
        // Create photo
        $query = <<<'QUERY'
        INSERT INTO photos(uuid, extension)
        VALUES(:uuid, :extension)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('uuid', $uuid, PDO::PARAM_STR);
        $statement->bindParam('extension', $extension, PDO::PARAM_STR);

        $statement->execute();

        // Add photo to the user
        $query = <<<'QUERY'
        UPDATE users SET
            profile_picture = :uuid
        WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('uuid', $uuid, PDO::PARAM_STR);
        $statement->bindParam('id', $idUser, PDO::PARAM_INT);

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

    public function changePassword(int $id, string $password) {
        $query = <<<'QUERY'
        UPDATE users SET
            password = :password
        WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();
    }
}
