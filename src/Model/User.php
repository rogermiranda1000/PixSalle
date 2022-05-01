<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class User
{

  private int $id;
  private string $username;
  private string $email;
  private string $password;
  private Datetime $createdAt;
  private Datetime $updatedAt;

  public function __construct(
    string $email,
    string $username,
    string $password,
    Datetime $createdAt,
    Datetime $updatedAt
  ) {
    $this->email = $email;
    if(empty($username) || !strcmp($email, $username)) {
        $this->username = substr($email, 0, stripos($email, '@'));
    } else {
        $this->username = $username;
    }
    $this->password = $password;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
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
}
