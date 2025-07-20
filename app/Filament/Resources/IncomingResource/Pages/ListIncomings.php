<?php

namespace App\Filament\Resources\IncomingResource\Pages;

use App\Filament\Exports\IncomingExporter;
use App\Filament\Resources\IncomingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class ListIncomings extends ListRecords
{
    protected static string $resource = IncomingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            ExportAction::make('Report')
                ->label('Report')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->exporter(IncomingExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ])
        ];
    }
}
