<?php

use App\Models\LighthouseAudit;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Lighthouse Performance Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $lhSortBy = 'performance_score';

    #[Session]
    public string $lhSortDirection = 'asc';

    #[Session]
    public string $formFactor = 'desktop';

    public function sort(string $column): void
    {
        $allowedColumns = [
            'monitors.url',
            'performance_score',
            'accessibility_score',
            'best_practices_score',
            'seo_score',
        ];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->lhSortBy === $column) {
            $this->lhSortDirection = $this->lhSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->lhSortBy = $column;
            $this->lhSortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function setFormFactor(string $factor): void
    {
        if (! in_array($factor, ['desktop', 'mobile'])) {
            return;
        }

        $this->formFactor = $factor;
        $this->resetPage();
    }

    #[Computed]
    public function audits()
    {
        $latestIds = LighthouseAudit::query()
            ->select(DB::raw('MAX(id)'))
            ->where('form_factor', $this->formFactor)
            ->groupBy('monitor_id');

        $sortColumn = in_array($this->lhSortBy, [
            'monitors.url',
            'performance_score',
            'accessibility_score',
            'best_practices_score',
            'seo_score',
        ]) ? $this->lhSortBy : 'performance_score';

        return LighthouseAudit::query()
            ->with('monitor')
            ->join('monitors', 'monitors.id', '=', 'lighthouse_audits.monitor_id')
            ->select('lighthouse_audits.*')
            ->whereIn('lighthouse_audits.id', $latestIds)
            ->orderBy($sortColumn, $this->lhSortDirection)
            ->paginate(50);
    }
};
