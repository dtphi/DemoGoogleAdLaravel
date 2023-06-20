<?php

namespace App\Repositories\Api\Users;

use App\Repositories\Api\Contacts\Users\UserContact;
use App\Models\Contacts\UserContact as UserModel;
use App\Http\Resources\Users\UserCollection;

final class UserRepository implements UserContact
{
  public function __construct(
    private UserModel $user
  ){}

  public function testData(): array
  {
    return [
      'id' => 1,
      'name' => 'Test name 1',
      'email' => 'testname1@gmail.com'
    ];
  }

  public function createUser(array $userFields)
  {
    return $this->user->insertDefault($userFields);
  }

  public function getListUserAll()
  {
    return new UserCollection($this->user->all());
  }
}
