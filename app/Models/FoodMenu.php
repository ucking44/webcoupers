<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
    use HasFactory;

    protected $table = 'food_menus';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'menu_id',
        'name',
        'price',
        'discount',
        'new_price',
        'description'
    ];

    public function menus(): HasMany
    {
        return $this->belongsTo(Menu::class);
    }
}
