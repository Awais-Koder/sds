<?php

namespace App\Filament\Resources\IncomingResource\Pages;

use App\Filament\Resources\IncomingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateIncoming extends CreateRecord
{
    protected static string $resource = IncomingResource::class;

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
