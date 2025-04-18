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
        Schema::create('rovers', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('x');
            $table->smallInteger('y');
            $table->string('direction', 1);
            $table->smallInteger('isActive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rovers');
    }
};
