<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
     use HasFactory; 
    protected $fillable = [
        'name', 'description', 'price', 'stock',
    ];

    // Example helper method to reduce stock
    public function reduceStock($quantity)
    {
        $this->stock -= $quantity;
        $this->save();
    }
}
