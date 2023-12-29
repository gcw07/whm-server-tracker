<?php

namespace App\Livewire\Server;

use App\Models\Server;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class NewToken extends ModalComponent
{
    use WireToast;

    public $server;

    /**
     * The component's state.
     */
    public array $state = [
        'token' => null,
    ];

    protected $validationAttributes = [
        'state.token' => 'token',
    ];

    public function mount(Server $server)
    {
        abort_if(auth()->guest(), 401);

        $this->server = $server;
    }

    public function render()
    {
        return view('livewire.server.new-token');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }

    protected function rules(): array
    {
        return [
            'state.token' => ['required', 'string'],
        ];
    }

    public function save()
    {
        $this->validate();

        $hadMissingToken = $this->server->missing_token;

        $this->server->update($this->state);

        if ($hadMissingToken) {
            toast()->success('The server api token was updated successfully.')->pushOnNextPage();

            return redirect()->route('servers.show', $this->server);
        }

        toast()->success('The server api token was updated successfully.')->push();
        $this->forceClose()->closeModal();
    }

    public function cancel()
    {
        $this->forceClose()->closeModal();
    }
}
