<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowedShip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ship_id',
        'total_price',
        'penalty_price',
        'returned_date',
        'returned_at',
    ];

    /**
     * Get the user that borrowed the ship.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ship that was borrowed.
     */
    public function ship()
    {
        return $this->belongsTo(Ship::class);
    }
}