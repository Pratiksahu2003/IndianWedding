<?php

namespace App\Livewire\Profile;

use App\Services\AuditLogger;
use App\Support\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My profile')]
class Edit extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $whatsapp = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->whatsapp = $user->whatsapp ?? '';
    }

    public function save(AuditLogger $audit): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $emailChanging = strcasecmp($this->email, $user->email) !== 0;
        $passwordChanging = trim($this->password) !== '';

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
        ];

        if ($emailChanging || $passwordChanging) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        if ($passwordChanging) {
            $rules['password'] = ['required', 'confirmed', 'min:10'];
        }

        $this->validate($rules, [
            'current_password.current_password' => 'Your current password is incorrect.',
        ]);

        $previousEmail = $user->email;

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => trim($this->phone) ?: null,
            'whatsapp' => trim($this->whatsapp) ?: null,
        ]);

        if ($passwordChanging) {
            $user->update([
                'password' => Hash::make($this->password),
            ]);
            $audit->log('auth.password_changed', $user);
        }

        if ($emailChanging) {
            $audit->log('profile.email_changed', $user, ['email' => $previousEmail], ['email' => $this->email]);
        }

        $this->reset('current_password', 'password', 'password_confirmation');
        session()->flash('status', $passwordChanging ? 'Profile and password updated.' : 'Profile updated.');
    }

    public function render()
    {
        $layout = auth()->user()?->isClient(Tenant::current())
            ? 'layouts.client'
            : 'layouts.studio';

        return view('livewire.profile.edit', [
            'dashboardRoute' => auth()->user()?->roleIn(Tenant::current())?->dashboardRoute() ?? 'app.dashboard',
        ])->layout($layout);
    }
}
