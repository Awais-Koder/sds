<?php

namespace App\Filament\Resources\SubmittelResource\Pages;

use App\Filament\Exports\IncomingExporter;
use App\Filament\Exports\SubmittelExporter;
use App\Filament\Resources\SubmittelResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;

class ListSubmittels extends ListRecords
{
    protected static string $resource = SubmittelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make('Export')
            ->label('Report')
            ->color('success')
            ->icon('heroicon-o-arrow-down-tray')
                ->exporter(SubmittelExporter::class)
            ->formats([
                ExportFormat::Xlsx,
                ExportFormat::Csv,
            ])
        ];
    }
}
