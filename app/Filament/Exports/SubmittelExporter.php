<?php

namespace App\Filament\Exports;

use App\Models\Submittel;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SubmittelExporter extends Exporter
{
    protected static ?string $model = Submittel::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('parent.ref_no')
                ->label('Parent\'s Ref No')
                ->formatStateUsing(fn ($state) => $state ?: 'N/A'),
            ExportColumn::make('user.name')
                ->label('Submitted By')
                ->formatStateUsing(fn ($state) => $state ?: 'N/A'),
            ExportColumn::make('submitted_time')
                ->formatStateUsing(fn ($state) => $state ?: 'N/A'),
            ExportColumn::make('approved.name')
                ->label('Approved By')
                ->formatStateUsing(fn ($state) => $state ?: 'N/A'),
            ExportColumn::make('update_time'),
            ExportColumn::make('name'),
            ExportColumn::make('ref_no'),
            ExportColumn::make('new_submittel')
                ->label('New Submittel')
                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
            ExportColumn::make('re_submittel')
                    ->label('Re Submittel')
                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
            ExportColumn::make('additional_copies')
                ->formatStateUsing(fn ($state) => $state ?? 'N/A'),
            ExportColumn::make('soft_copy')
                ->label('Soft Copy')
                ->formatStateUsing(fn ($state) => $state ? 'True' : 'False'),
            ExportColumn::make('date'),
            ExportColumn::make('cycle'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at')
                ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d H:i')),
            ExportColumn::make('updated_at')
                ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d H:i')),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your submittel export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
