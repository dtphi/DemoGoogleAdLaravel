<?php

namespace App\Services\Api\Users;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
      $this->app->bind(
        \App\Repositories\Api\Contacts\Users\UserContact::class,
        \App\Repositories\Api\Users\UserRepository::class
      );
      $this->app->bind('user', function() {
          return new \App\Services\Api\Users\UserService;
      });
    }
}
