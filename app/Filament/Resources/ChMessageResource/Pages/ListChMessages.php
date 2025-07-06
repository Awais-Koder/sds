<?php

namespace App\Filament\Resources\ChMessageResource\Pages;

use App\Filament\Resources\ChMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChMessages extends ListRecords
{
    protected static string $resource = ChMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
