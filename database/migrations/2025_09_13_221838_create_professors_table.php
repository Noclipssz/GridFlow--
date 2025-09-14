<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('professores', function (Blueprint $table) {
            $table->tinyIncrements('id');                 // Byte (0–255)
            $table->string('nome');
            $table->unsignedTinyInteger('materia_id');    // FK para materias.id
            $table->json('horario_dp')->nullable();       // byte[][]
            $table->timestamps();

            $table->foreign('materia_id')
                ->references('id')->on('materias')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->dropForeign(['materia_id']);
        });
        Schema::dropIfExists('professores');
    }
};
