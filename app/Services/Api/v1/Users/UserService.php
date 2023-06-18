<?php

namespace App\Services\Api\Users;

use App\Repositories\Api\Contacts\Users\UserContact;

class UserService
{
  public function __construct(
    private UserContact $userRepo
  ){
  }

  public function userRepository()
  {
    return $this->userRepo;
  }
}
