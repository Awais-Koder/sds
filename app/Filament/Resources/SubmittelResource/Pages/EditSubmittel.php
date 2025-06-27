<?php

namespace App\Filament\Resources\SubmittelResource\Pages;

use App\Filament\Resources\SubmittelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubmittel extends EditRecord
{
    protected static string $resource = SubmittelResource::class;
    public $shopDrawingsTemp;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->shopDrawingsTemp = $data['Shop Drawings'] ?? [];
        unset($data['Shop Drawings']);
        return $data;
    }

    protected function afterSave()
    {
        foreach ($this->shopDrawingsTemp as $drawing) {
            $this->record->outgoings()->create($drawing);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
