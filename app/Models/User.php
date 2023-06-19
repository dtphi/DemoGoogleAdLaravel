<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Contacts\UserContact;
use Illuminate\Support\Facades\Hash;
use Carbon;

class User extends Authenticatable implements UserContact
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];

    public function getCreatedAtAttribue($value)
    {
        Carbon::createFromFormat('Y-MM-dd',$value);

        return $value;
    }

    public function insertDefault(array $user)
    {
        $result = $this->create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $user['password']
        ]);
        
        return $result;
    }
}
