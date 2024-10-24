<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The ships that belong to the category.
     */
    public function ships()
    {
        return $this->belongsToMany(Ship::class, 'ship_category_ship');
    }
}