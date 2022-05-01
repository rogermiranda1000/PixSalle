<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\User;
use Salle\PixSalle\Model\Photo;

interface UserRepository
{
    public function createUser(User $user): void;
    public function getUserByEmail(string $email);
    public function changePassword(int $id, string $password);
    public function modifyUserBasic($id, $username, $phone): void;

    // membership
    public function getUserById(int $id);
    public function setUserMembership(int $id, string $new_membership): void;

    // explore
    public function getPhotos();
    public function createPhoto($idUser, $uuid, $extension);
}
