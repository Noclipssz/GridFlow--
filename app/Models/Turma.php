<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Turma extends Model
{
    protected $fillable = [
        'nome',
        'ano_letivo',
        'periodo',
        'capacidade_alunos',
        'observacoes',
    ];

    /**
     * The materias that belong to the Turma
     */
    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'materia_turma');
    }

    /**
     * The professors that belong to the Turma
     */
    public function professors(): BelongsToMany
    {
        return $this->belongsToMany(Professor::class, 'professor_turma');
    }
}


