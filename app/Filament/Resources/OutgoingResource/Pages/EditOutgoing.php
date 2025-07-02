<?php

namespace App\Filament\Resources\OutgoingResource\Pages;

use App\Filament\Resources\OutgoingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditOutgoing extends EditRecord
{
    protected static string $resource = OutgoingResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['submitted_by'] = Auth::id();
        $data['submitted_time'] = now();
        if(!empty($data['cycle'])){
            $data['cycle'] = 0;
        }
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
