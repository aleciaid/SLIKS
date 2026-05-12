<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.change-password';

    protected static ?string $title = 'Ubah Password';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('current_password')
                    ->label('Password Saat Ini')
                    ->password()
                    ->required()
                    ->currentPassword(),
                Forms\Components\TextInput::make('password')
                    ->label('Password Baru')
                    ->password()
                    ->required()
                    ->rule(Password::defaults())
                    ->confirmed(),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password Baru')
                    ->password()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        $this->form->fill();

        Notification::make()
            ->title('Password berhasil diubah')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Password')
                ->submit('save'),
        ];
    }
}
