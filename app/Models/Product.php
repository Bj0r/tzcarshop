<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'year',
        'color',
        'transmission',
        'fuel_type',
        'description',
        'price',
        'stock',
        'image',
    ];
}
