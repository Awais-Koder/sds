<?php

namespace App\Filament\Resources\SubmittelResource\Pages;

use App\Filament\Resources\SubmittelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubmittels extends ListRecords
{
    protected static string $resource = SubmittelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
