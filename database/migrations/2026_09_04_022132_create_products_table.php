<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('category')->default('Toyota');

            $table->unsignedSmallInteger('year')->default(2025);
            $table->string('color')->default('White');
            $table->string('transmission')->default('Automatic');
            $table->string('fuel_type')->default('Gasoline');

            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);

            $table->integer('stock')->default(0);

            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
