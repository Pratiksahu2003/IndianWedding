<?php

namespace App\Livewire\Studio\Messages;

use App\Models\Message;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Messages')]
class Index extends Component
{
    public string $body = '';

    public function send(): void
    {
        $this->validate(['body' => ['required', 'string', 'max:4000']]);
        Message::query()->create([
            'organization_id' => Tenant::id(),
            'sender_id' => auth()->id(),
            'body' => $this->body,
        ]);
        $this->body = '';
    }

    public function render()
    {
        return view('livewire.studio.messages.index', [
            'messages' => Message::query()->with('sender')->latest()->limit(40)->get()->reverse(),
        ]);
    }
}
