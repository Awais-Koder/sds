<?php

namespace App\Filament\Resources\OutgoingResource\Pages;

use App\Filament\Resources\OutgoingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Exports\OutgoingExporter;

class ListOutgoings extends ListRecords
{
    protected static string $resource = OutgoingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make('Export')
            ->label('Export')
            ->color('success')
            ->icon('heroicon-o-arrow-down-tray')
                ->exporter(OutgoingExporter::class)
            ->formats([
                ExportFormat::Xlsx,
                ExportFormat::Csv,
            ])
        ];
    }
}
