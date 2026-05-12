<?php

namespace App\Filament\Resources\CollectivResource\Pages;

use App\Filament\Resources\CollectivResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCollectiv extends EditRecord
{
    protected static string $resource = CollectivResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
