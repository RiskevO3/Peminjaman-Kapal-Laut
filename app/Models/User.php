<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Set the role attribute.
     *
     * @param string $value
     * @return void
     */
    public function setRoleAttribute(string $value): void
    {
        $allowedRoles = ['user', 'admin'];
        if (!in_array($value, $allowedRoles)) {
            throw new \InvalidArgumentException("Invalid role: $value");
        }
        $this->attributes['role'] = $value;
    }
    
    /**
     * Get the ships borrowed by the user.
     */
    public function borrowedShips()
    {
        return $this->hasMany(BorrowedShip::class);
    }
}
