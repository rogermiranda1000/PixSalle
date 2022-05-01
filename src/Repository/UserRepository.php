<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\User;
use Salle\PixSalle\Model\Photo;

interface UserRepository
{
    public function createUser(User $user): void;
    public function getUserByEmail(string $email);

    //Wallet
    public function modifyWallet(int $id, float $amount): void;

    // membership
    public function getUserById(int $id);
    public function setUserMembership(int $id, string $new_membership): void;

    // explore
    public function getPhotos();
}
