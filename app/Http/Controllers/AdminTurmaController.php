<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use Illuminate\Http\Request;

class AdminTurmaController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->input('periodo', 'manha');

        $turmas = Turma::orderBy('id')->get();
        $turmasDoPeriodo = $turmas->filter(fn($t) => $t->periodo === $periodo);

        return view('admin.turmas', [
            'periodo' => $periodo,
            'turmas'  => $turmasDoPeriodo,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'    => ['required', 'string', 'max:255'],
            'periodo' => ['required', 'in:manha,tarde,noite'],
        ]);

        $t = new Turma();
        $t->nome = $data['nome'];
        $t->periodo = $data['periodo'];
        // grades iniciam vazias
        $t->horario_manha = null;
        $t->horario_tarde = null;
        $t->horario_noite = null;
        $t->save();

        return redirect()->route('admin.turmas.index', ['periodo' => $data['periodo']])
            ->with('ok', 'Turma criada com sucesso.');
    }
}

