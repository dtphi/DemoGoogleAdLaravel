<?php

namespace App\Models\Contacts;

interface UserContact
{
    public function insertDefault(array $user);
}