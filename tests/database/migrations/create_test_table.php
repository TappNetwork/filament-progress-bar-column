<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('stock')->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('max_quantity')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_models');
    }
};
