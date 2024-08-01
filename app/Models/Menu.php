<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'name',
        'description'
    ];

    public function foodmenus(): HasMany
    {
        return $this->hasMany(FoodMenu::class);
    }

    /**
     * The comments associated to the post.
     */
    public function drinkmenus(): HasMany
    {
        return $this->hasMany(DrinkMenu::class);
    }
}
