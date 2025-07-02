<?php

namespace App\Filament\Resources\IncomingResource\Pages;

use App\Filament\Resources\IncomingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditIncoming extends EditRecord
{
    protected static string $resource = IncomingResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['approved_by'] = Auth::id();
        $data['update_time'] = now();
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
