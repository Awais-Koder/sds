<?php

namespace App\Filament\Resources\SubmittelResource\Pages;

use App\Filament\Resources\SubmittelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSubmittel extends EditRecord
{
    protected static string $resource = SubmittelResource::class;
    public $shopDrawingsTemp;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['new_submittel'])) {
            $data['cycle'] = 0;
        }
        $data['submitted_by'] = Auth::id();
        $data['submitted_time'] = now();
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
