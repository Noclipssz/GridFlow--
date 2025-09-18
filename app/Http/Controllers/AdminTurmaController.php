<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Professor;
use App\Models\Turma;
use Illuminate\Http\Request;

class AdminTurmaController extends Controller
{
    public function index()
    {
        $turmas = Turma::all();
        return view("admin.turmas.index", compact("turmas"));
    }

    public function create()
    {
        return view("admin.turmas.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "nome" => "required|string|max:255",
            "ano_letivo" => "required|integer",
            "periodo" => "required|string|max:255",
            "capacidade_alunos" => "required|integer",
        ]);

        Turma::create($request->all());

        return redirect()->route("admin.turmas.index")->with("success", "Turma criada com sucesso!");
    }

    public function edit(Turma $turma)
    {
        return view("admin.turmas.edit", compact("turma"));
    }

    public function update(Request $request, Turma $turma)
    {
        $request->validate([
            "nome" => "required|string|max:255",
            "ano_letivo" => "required|integer",
            "periodo" => "required|string|max:255",
            "capacidade_alunos" => "required|integer",
        ]);

        $turma->update($request->all());

        return redirect()->route("admin.turmas.index")->with("success", "Turma atualizada com sucesso!");
    }

    public function destroy(Turma $turma)
    {
        $turma->delete();
        return redirect()->route("admin.turmas.index")->with("success", "Turma excluída com sucesso!");
    }

    public function associar(Turma $turma)
    {
        $materias = Materia::all();
        $professores = Professor::all();
        return view("admin.turmas.associar", compact("turma", "materias", "professores"));
    }

    public function salvarAssociacao(Request $request, Turma $turma)
    {
        $turma->materias()->sync($request->input("materias", []));
        $turma->professors()->sync($request->input("professores", []));

        return redirect()->route("admin.turmas.index")->with("success", "Associações salvas com sucesso!");
    }

    public function show(Turma $turma)
    {
        $turma->load('horarios');
        return view('admin.turmas.show', compact('turma'));
    }
}


