<?php

use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Settings')] class extends Component
{
    public function mount(): void
    {
        if (session()->has('settings.toast')) {
            $toast = session('settings.toast');

            Flux::toast(
                text: $toast['text'],
                heading: $toast['heading'],
                variant: $toast['variant'],
            );
        }
    }

    #[Computed]
    public function googleConnection(): ?array
    {
        return Setting::getValue('google_oauth');
    }
};
