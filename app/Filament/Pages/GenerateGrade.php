<?php

namespace App\Filament\Pages;

use App\Models\Materia;
use App\Models\Professor;
use App\Models\Turma;
use App\Services\GradeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Get;

class GenerateGrade extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Academico';
    protected static string $view = 'filament.pages.generate-grade';
    protected static ?string $title = 'Gerar Grade';
    protected ?string $maxContentWidth = 'full';

    public ?string $periodo = 'manha';
    public ?int $turma_id = null;
    public array $selected = [];
    public array $grid = [];
    public array $meta = [];
    public array $grid_ids = [];
    public bool $only_available = false;
    public bool $auto_generate = true;
    public bool $suppress_auto_once = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('periodo')
                    ->label('Período')
                    ->options(['manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'])
                    ->live()
                    ->required(),
                Forms\Components\Select::make('turma_id')
                    ->label('Turma')
                    ->options(fn () => $this->availableTurmaOptions())
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('only_available')
                    ->label('Mostrar apenas professores com disponibilidade no período')
                    ->live(),
                Forms\Components\Toggle::make('auto_generate')
                    ->label('Gerar automaticamente ao alterar escolhas')
                    ->live(),
                Forms\Components\Repeater::make('selected')
                    ->label('Professores por matéria')
                    ->schema([
                        Forms\Components\Select::make('materia_id')
                            ->options(function () {
                                $query = Materia::orderBy('id');
                                $scheduled = $this->getScheduledMateriaIds();
                                if (!empty($scheduled)) {
                                    $query->whereNotIn('id', $scheduled);
                                }
                                return $query->where('check', true)->pluck('nome', 'id');
                            })
                            ->label('Matéria')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('professor_id')
                            ->options(function (Get $get) {
                                $mid = (int) ($get('materia_id') ?? 0);
                                if (!$mid) return [];
                                if ($this->only_available) {
                                    $per = (string) $this->periodo;
                                    return Professor::where('materia_id', $mid)->get()
                                        ->filter(fn ($p) => $this->hasAvailability($p, $per))
                                        ->sortBy('nome')->pluck('nome', 'id');
                                }
                                return Professor::where('materia_id', $mid)->orderBy('nome')->pluck('nome', 'id');
                    })
                    ->label('Professor responsável')
                    ->searchable()
                    ->preload()
                    ->native(false),
                    ])
                    ->columns(2)
                    ->grid(2)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->default(function () {
                        $scheduled = $this->getScheduledMateriaIds();
                        return Materia::orderBy('id')
                            ->where('check', true)
                            ->when(!empty($scheduled), fn ($q) => $q->whereNotIn('id', $scheduled))
                            ->get()
                            ->map(fn ($m) => ['materia_id' => $m->id, 'professor_id' => null])
                            ->toArray();
                    })
                    ->addActionLabel('Adicionar matéria')
            ])
            ->columns(2);
    }

    public function generate(): void
    {
        $pairs = collect($this->selected)
            ->filter(fn ($row) => !empty($row['professor_id']))
            ->pluck('professor_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $professors = Professor::with('materia')->whereIn('id', $pairs)->get();
        [$grid, $meta, $idsGrid] = (new GradeService())->buildGrid($professors, (string) $this->periodo);
        // Enriquecer meta com contadores úteis
        $desired = $professors->sum(fn ($p) => (int) ($p->materia->quant_aulas ?? 0));
        $placed = 0;
        foreach ($idsGrid as $aRow) {
            foreach ((array) $aRow as $cell) {
                if (is_array($cell) && !empty($cell['professor_id'])) $placed++;
            }
        }
        $meta['desired_total'] = $desired;
        $meta['placed_total'] = $placed;
        $meta['missing_total'] = max(0, $desired - $placed);

        // Detalhar pendências por professor usando meta['remaining']
        $remaining = (array) ($meta['remaining'] ?? []);
        $profMap = $professors->keyBy('id');
        $problems = [];
        foreach ($remaining as $pid => $miss) {
            $miss = (int) $miss;
            if ($miss <= 0) continue;
            $p = $profMap[$pid] ?? null;
            if (!$p) continue;
            $problems[] = [
                'professor_id' => (int) $pid,
                'professor'    => (string) ($p->nome ?? ''),
                'materia'      => (string) ($p->materia->nome ?? ''),
                'missing'      => $miss,
                'desired'      => (int) ($p->materia->quant_aulas ?? 0),
                'available'    => $this->countAvailability($p, (string) $this->periodo),
            ];
        }
        $meta['problems'] = $problems;

        $this->grid = $grid; $this->meta = $meta; $this->grid_ids = $idsGrid;
        if (!empty($problems)) {
            Notification::make()->title('Grade gerada com pendências')->warning()->send();
        } else {
            Notification::make()->title('Grade gerada')->success()->send();
        }
    }

    public function save(): void
    {
        $turma = Turma::findOrFail((int) $this->turma_id);

        // Validação estrita: não salvar se algum slot proposto conflitar com disponibilidade atual (0) ou já ocupado (2)
        $conflicts = [];
        for ($a = 0; $a < 5; $a++) {
            $row = $this->grid_ids[$a] ?? [];
            for ($d = 0; $d < 5; $d++) {
                $cell = $row[$d] ?? null;
                if (!is_array($cell) || empty($cell['professor_id'])) continue;
                $pid = (int) $cell['professor_id'];
                $p = Professor::with('materia')->find($pid);
                if (!$p) { $conflicts[] = "Professor #{$pid} inexistente"; continue; }
                $h = match((string) $this->periodo) {
                    'manha' => $p->horario_manha,
                    'tarde' => $p->horario_tarde,
                    'noite' => $p->horario_noite,
                };
                if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
                $v = (int) ($h[$d][$a] ?? 0);
                if ($v !== 1) {
                    $conflicts[] = ($p->nome ?? 'Professor') . " — dia " . ($d+1) . ", aula " . ($a+1);
                }
            }
        }

        if (!empty($conflicts)) {
            Notification::make()->title('Conflitos detectados, grade não salva')->warning()->body(
                'Ajuste disponibilidades/seleções. Ex.: ' . implode(', ', array_slice($conflicts, 0, 3)) . (count($conflicts) > 3 ? '...' : '')
            )->send();
            return;
        }

        (new GradeService())->saveGrid($turma, $this->grid_ids, (string) $this->periodo);
        Notification::make()->title('Grade salva')->success()->send();
        // Após salvar, limpar seleção e atualizar a lista de turmas disponíveis
        $this->grid = [];
        $this->grid_ids = [];
        $this->meta = [];
        $this->selected = $this->buildDefaultSelectionRows();
        $this->turma_id = null;
    }

    public function fillDefaults(): void
    {
        $periodo = (string) $this->periodo;
        $scheduled = $this->getScheduledMateriaIds();
        $rows = Materia::orderBy('id')
            ->where('check', true)
            ->when(!empty($scheduled), fn ($q) => $q->whereNotIn('id', $scheduled))
            ->get()->map(function ($m) use ($periodo) {
            $prof = Professor::where('materia_id', $m->id)->get();
            if ($this->only_available) {
                $prof = $prof->filter(fn ($p) => $this->hasAvailability($p, $periodo));
            }
            $first = $prof->sortBy('nome')->first();
            return [
                'materia_id' => $m->id,
                'professor_id' => $first?->id,
            ];
        })->toArray();
        $this->selected = $rows;
        // não gerar automaticamente ao preencher automaticamente
        $this->suppress_auto_once = true;
    }

    public function clearSelection(): void
    {
        $this->selected = collect($this->selected)->map(function ($row) {
            return ['materia_id' => $row['materia_id'] ?? null, 'professor_id' => null];
        })->toArray();
        $this->grid = [];
        $this->grid_ids = [];
        $this->meta = [];
    }

    public function updatedPeriodo(): void
    {
        // limpar prévia ao trocar período e (talvez) regenerar
        $this->grid = []; $this->grid_ids = []; $this->meta = [];
        // se a turma atual ficou indisponível nesse período, resetar
        if ($this->turma_id) {
            $t = Turma::find($this->turma_id);
            if (!$t || $t->periodo !== $this->periodo || $this->turmaHasSchedule($t, (string) $this->periodo)) {
                $this->turma_id = null;
            }
        }
        $this->maybeAutoGenerate();
    }

    public function updatedTurmaId(): void
    {
        // Ao escolher turma, se já estiver com grade, anula seleção
        if ($this->turma_id) {
            $t = Turma::find($this->turma_id);
            if ($t && $this->turmaHasSchedule($t, (string) $this->periodo)) {
                $this->turma_id = null;
                Notification::make()->title('Turma já possui grade neste período')->warning()->send();
                return;
            }
        }
        // Recarrega matérias excluindo as já agendadas
        $this->selected = $this->buildDefaultSelectionRows();
        $this->maybeAutoGenerate();
    }

    public function updatedSelected(): void
    {
        $this->maybeAutoGenerate();
    }

    private function maybeAutoGenerate(): void
    {
        if ($this->suppress_auto_once) { $this->suppress_auto_once = false; return; }
        if (!$this->auto_generate) return;
        $hasAny = collect($this->selected)->contains(fn ($r) => !empty($r['professor_id']));
        if ($this->turma_id && $hasAny) {
            $this->generate();
        }
    }

    private function hasAvailability(Professor $p, string $periodo): bool
    {
        $h = match($periodo) {
            'manha' => $p->horario_manha,
            'tarde' => $p->horario_tarde,
            'noite' => $p->horario_noite,
        };
        if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
        foreach ($h as $day) {
            if (!is_array($day)) continue;
            foreach ($day as $v) { if ((int)$v === 1) return true; }
        }
        return false;
    }

    private function countAvailability(Professor $p, string $periodo): int
    {
        $h = match($periodo) {
            'manha' => $p->horario_manha,
            'tarde' => $p->horario_tarde,
            'noite' => $p->horario_noite,
        };
        if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
        $count = 0;
        for ($d = 0; $d < 5; $d++) {
            $row = $h[$d] ?? [];
            for ($a = 0; $a < 5; $a++) {
                if ((int) ($row[$a] ?? 0) === 1) $count++;
            }
        }
        return $count;
    }

    private function getScheduledMateriaIds(): array
    {
        $tid = (int) ($this->turma_id ?? 0);
        if (!$tid) return [];
        $t = Turma::find($tid);
        if (!$t) return [];
        $grid = (new GradeService())->getTurmaGrid($t, (string) $this->periodo);
        if (!is_array($grid)) $grid = json_decode((string) $grid, true) ?? [];
        $ids = [];
        foreach ($grid as $row) {
            if (!is_array($row)) continue;
            foreach ($row as $cell) {
                if (is_array($cell) && !empty($cell['materia_id'])) $ids[] = (int) $cell['materia_id'];
            }
        }
        return array_values(array_unique($ids));
    }

    private function buildDefaultSelectionRows(): array
    {
        $scheduled = $this->getScheduledMateriaIds();
        return Materia::orderBy('id')
            ->where('check', true)
            ->when(!empty($scheduled), fn ($q) => $q->whereNotIn('id', $scheduled))
            ->get()
            ->map(fn ($m) => ['materia_id' => $m->id, 'professor_id' => null])
            ->toArray();
    }

    private function turmaHasSchedule(Turma $turma, string $periodo): bool
    {
        $grid = (new GradeService())->getTurmaGrid($turma, $periodo);
        if (!is_array($grid)) $grid = json_decode((string) $grid, true) ?? [];
        for ($a = 0; $a < 5; $a++) {
            $row = $grid[$a] ?? [];
            for ($d = 0; $d < 5; $d++) {
                $cell = $row[$d] ?? null;
                if (is_array($cell) && (!empty($cell['professor_id']) || !empty($cell['materia_id']))) {
                    return true;
                }
            }
        }
        return false;
    }

    private function availableTurmaOptions(): array
    {
        return Turma::query()
            ->where('periodo', (string) $this->periodo)
            ->orderBy('id')
            ->get()
            ->filter(fn ($t) => !$this->turmaHasSchedule($t, (string) $this->periodo))
            ->pluck('nome', 'id')
            ->toArray();
    }
}
