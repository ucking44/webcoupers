<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('menu_id')->nullable();
            $table->string('name');
            $table->double('price', 14, 2);
            $table->double('discount', 14, 2)->nullable();
            $table->double('new_price', 14, 2)->nullable();
            $table->longText('description')->nullable();
            $table->foreign('user_id')->references('id')->on('users'); //onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus'); //onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_menus');
    }
};
