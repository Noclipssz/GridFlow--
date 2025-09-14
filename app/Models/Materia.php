<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    protected $table = 'materias';

    protected $fillable = [
        'nome',
        'quant_aulas',
        'check',
    ];

    protected $casts = [
        'quant_aulas' => 'integer',
        'check' => 'boolean',
    ];

    public function professores(): HasMany
    {
        return $this->hasMany(Professor::class, 'materia_id');
    }
}
