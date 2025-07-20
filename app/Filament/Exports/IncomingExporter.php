<?php

namespace App\Filament\Exports;

use App\Models\Submittel;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class IncomingExporter extends Exporter
{
    protected static ?string $model = Submittel::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('ref_no'),
            ExportColumn::make('send_by_dc_to_actioner')->label('Received at'),
            ExportColumn::make('user.name')->label('Submitted By'),
            
            ExportColumn::make('status'),
            ExportColumn::make('created_at')
                ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d H:i')),
            ExportColumn::make('updated_at')
                ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your incoming export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
