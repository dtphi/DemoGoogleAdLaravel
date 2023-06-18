<?php

namespace App\Repositories\Api\Users;

use App\Repositories\Api\Contacts\Users\UserContact;

final class UserRepository implements UserContact
{
  public function testData(): array
  {
    return [
      'id' => 1,
      'name' => 'Test name 1',
      'email' => 'testname1@gmail.com'
    ];
  }
}
