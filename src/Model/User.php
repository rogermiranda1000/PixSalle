<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class User
{

  private int $id;
  private ?string $username;
  private string $email;
  private string $password;
  private Datetime $createdAt;
  private Datetime $updatedAt;
  private ?string $phone;

  public function __construct(
    string $email,
    ?string $username,
    string $password,
    Datetime $createdAt,
    Datetime $updatedAt,
    ?string $phone
  ) {
    $this->email = $email;
    $this->username = $username;
    $this->password = $password;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->phone = $phone;
  }

  public function id()
  {
    return $this->id;
  }

  public function username()
  {
    return $this->username;
  }

  public function email()
  {
    return $this->email;
  }

  public function password()
  {
    return $this->password;
  }

  public function createdAt()
  {
    return $this->createdAt;
  }

  public function updatedAt()
  {
    return $this->updatedAt;
  }

  public function phone()
  {
      return $this->phone;
  }

  public function setUsername($username) {
      $this->username = $username;
  }
}
