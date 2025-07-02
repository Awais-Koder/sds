<?php

namespace App\Filament\Resources\OutgoingResource\Pages;

use App\Filament\Resources\OutgoingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOutgoing extends CreateRecord
{
    protected static string $resource = OutgoingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['submitted_by'] = Auth::id();
        $data['submitted_time'] = now();
        if(!empty($data['cycle'])){
            $data['cycle'] = 0;
        }
        return $data;
    }
}
