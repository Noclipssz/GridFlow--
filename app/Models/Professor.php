<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class Professor extends Model
{
    protected $table = 'professores';

    protected $fillable = [
        'nome',
        'cpf',
        'senha',
        'materia_id',
    ];

    protected $hidden = [
        'senha',
    ];

    protected $casts = [
        'horario_manha' => 'array',
        'horario_tarde' => 'array',
        'horario_noite' => 'array',
        'materia_id' => 'integer',
    ];

    // Hash automático da senha ao atribuir
    public function setSenhaAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['senha'] = $value;
            return;
        }

        // Evita re-hash se já estiver hasheada
        $this->attributes['senha'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }
}
