<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['pdf_images'])) {
            $data['pdf_images'] = json_encode($data['pdf_images'] ?? []);
        } else {
            // otherwise remove it entirely so Eloquent doesn't try to use null
            unset($data['pdf_images']);
        }
        return $data;
    }
}
