<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    protected $table = 'turmas';

    protected $fillable = [
        'nome',
        'horario_dp',
    ];

    protected $casts = [
        'horario_dp' => 'array', // [aula][dia] => { professor_id, materia_id } | null
    ];
}

