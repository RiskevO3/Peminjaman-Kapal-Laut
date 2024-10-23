<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ship_name',
        'total_unit',
        'borrowed_unit',
        'available_unit',
        'price',
        'penalty_fee',
    ];

    /**
     * Get the users who borrowed the ship.
     */
    public function borrowedShips()
    {
        return $this->hasMany(BorrowedShip::class);
    }
}