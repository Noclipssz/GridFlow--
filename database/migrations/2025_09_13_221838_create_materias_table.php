<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->tinyIncrements('id');                 // Byte (0–255)
            $table->string('nome');
            $table->unsignedTinyInteger('quant_aulas');   // Byte (0–255)
            $table->boolean('check')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
