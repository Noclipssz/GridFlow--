<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    protected $table = 'turmas';

    protected $fillable = [
        'nome',
        'periodo',
    ];

    protected $casts = [
        'horario_manha' => 'array',
        'horario_tarde' => 'array',
        'horario_noite' => 'array',
    ];
}
