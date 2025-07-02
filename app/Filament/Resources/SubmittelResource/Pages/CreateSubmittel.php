<?php

namespace App\Filament\Resources\SubmittelResource\Pages;

use App\Filament\Resources\SubmittelResource;
use App\Models\Submittel;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSubmittel extends CreateRecord
{
    protected static string $resource = SubmittelResource::class;

    public $shopDrawingsTemp = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['new_submittel'])) {
            $data['cycle'] = 0;
        }
        $data['submitted_by'] = Auth::id();
        $data['submitted_time'] = now();
        $data['status'] = 'submitted';
        return $data;
    }
}
