<?php

namespace App\Filament\Exports;

use App\Models\Incoming;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class IncomingExporter extends Exporter
{
    protected static ?string $model = Incoming::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('category.name'),
            ExportColumn::make('submittel.ref_no')
            ->label('Submittel Ref No'),
            ExportColumn::make('file'),
            ExportColumn::make('sds_no'),
            ExportColumn::make('dwg_no'),
            ExportColumn::make('description'),
            ExportColumn::make('cycle'),
            ExportColumn::make('no_of_copies'),
            ExportColumn::make('user.name'),
            ExportColumn::make('submitted_time'),
            ExportColumn::make('approved.name'),
            ExportColumn::make('update_time'),
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
