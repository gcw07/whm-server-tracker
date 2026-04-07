<?php

use App\Models\LighthouseAudit;
use App\Models\Monitor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Lighthouse Report')] class extends Component
{
    public Monitor $monitor;

    public string $domainUrl;

    public string $formFactor = 'desktop';

    public $audits;

    public $selectedAudit;

    public function mount(Monitor $monitor): void
    {
        $this->monitor = $monitor;
        $this->domainUrl = preg_replace('(^https?://)', '', $this->monitor->url);
        $this->loadAudits();
    }

    public function switchFormFactor(string $formFactor): void
    {
        $this->formFactor = $formFactor;
        $this->loadAudits();
    }

    public function changeSelected(LighthouseAudit $audit): void
    {
        $this->selectedAudit = $audit;
    }

    private function loadAudits(): void
    {
        $this->audits = $this->monitor->lighthouseLatestAudit()
            ->where('form_factor', $this->formFactor)
            ->get();
        $this->selectedAudit = $this->audits->first();
    }

    /**
     * @return array<string, array{label: string, value: string, rating: string}>
     */
    #[Computed]
    public function metrics(): array
    {
        if (! $this->selectedAudit) {
            return [];
        }

        $audit = $this->selectedAudit;

        return [
            'fcp' => [
                'label' => 'First Contentful Paint',
                'abbr' => 'FCP',
                'value' => $this->formatMs($audit->first_contentful_paint),
                'rating' => $this->rateMs($audit->first_contentful_paint, 1800, 3000),
            ],
            'lcp' => [
                'label' => 'Largest Contentful Paint',
                'abbr' => 'LCP',
                'value' => $this->formatMs($audit->largest_contentful_paint),
                'rating' => $this->rateMs($audit->largest_contentful_paint, 2500, 4000),
            ],
            'tti' => [
                'label' => 'Time to Interactive',
                'abbr' => 'TTI',
                'value' => $this->formatMs($audit->time_to_interactive),
                'rating' => $this->rateMs($audit->time_to_interactive, 3800, 7300),
            ],
            'tbt' => [
                'label' => 'Total Blocking Time',
                'abbr' => 'TBT',
                'value' => $this->formatMs($audit->total_blocking_time),
                'rating' => $this->rateMs($audit->total_blocking_time, 200, 600),
            ],
            'cls' => [
                'label' => 'Cumulative Layout Shift',
                'abbr' => 'CLS',
                'value' => $audit->cumulative_layout_shift !== null ? number_format($audit->cumulative_layout_shift, 3) : '—',
                'rating' => $this->rateCls($audit->cumulative_layout_shift),
            ],
            'si' => [
                'label' => 'Speed Index',
                'abbr' => 'SI',
                'value' => $this->formatMs($audit->speed_index),
                'rating' => $this->rateMs($audit->speed_index, 3400, 5800),
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, description: string|null, displayValue: string|null, score: float}>
     */
    #[Computed]
    public function opportunities(): array
    {
        if (! $this->selectedAudit?->raw_results) {
            return [];
        }

        $audits = json_decode($this->selectedAudit->raw_results, true);

        return collect($audits)
            ->filter(fn ($audit) => ($audit['scoreDisplayMode'] ?? '') === 'metricSavings'
                && isset($audit['score'])
                && $audit['score'] < 1
            )
            ->sortBy('score')
            ->map(fn ($audit) => [
                'title' => $audit['title'],
                'description' => $audit['description'] ?? null,
                'displayValue' => $audit['displayValue'] ?? null,
                'score' => $audit['score'],
            ])
            ->values()
            ->all();
    }

    private function formatMs(?int $ms): string
    {
        if ($ms === null) {
            return '—';
        }

        if ($ms < 1000) {
            return $ms.' ms';
        }

        return number_format($ms / 1000, 1).' s';
    }

    private function rateMs(?int $ms, int $goodThreshold, int $poorThreshold): string
    {
        if ($ms === null) {
            return 'unknown';
        }

        if ($ms < $goodThreshold) {
            return 'good';
        }

        if ($ms < $poorThreshold) {
            return 'needs-improvement';
        }

        return 'poor';
    }

    private function rateCls(?float $cls): string
    {
        if ($cls === null) {
            return 'unknown';
        }

        if ($cls < 0.1) {
            return 'good';
        }

        if ($cls < 0.25) {
            return 'needs-improvement';
        }

        return 'poor';
    }
};
