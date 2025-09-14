<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('professores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('restrict');
            $table->json("horario_dp")->nullable();       // byte[][]
            $table->timestamps();
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
