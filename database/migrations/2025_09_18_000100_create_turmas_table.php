<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('turmas', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nome');
            $table->json('horario_dp')->nullable(); // [aula][dia] => { professor_id, materia_id }
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};

