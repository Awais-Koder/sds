<?php

namespace App\Filament\Pages;

use App\Models\Submittel;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\SubmittelFinalReport as SubmittelFinalReportModel;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class SubmittelFinalReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.submittel-final-report';

    public static function table(Table $table): Table
    {
        return $table
            ->query(SubmittelFinalReportModel::query())
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('submittel.approved.name')
                    ->label('Approved By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('submittel.update_time')
                    ->date()
                    ->label('Approved At')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ref_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'under_review' => 'primary',
                        'approved_as_noted' => 'teal',
                        'revise_and_resubmit' => 'lime',
                        'rejected' => 'danger',
                    })
                    ->searchable()
                    ->formatStateUsing(function (string $state): string {
                        return str_replace('_', ' ', ucfirst(strtolower($state)));
                    }),
                Tables\Columns\TextColumn::make('comments')
                    ->limit(50),
            ])
            ->filters([
                Filter::make('created_at_range')
                    ->label('Created Between')
                    ->form([
                        DatePicker::make('from')->label('From date'),
                        DatePicker::make('until')->label('To date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn($query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Download')
                    ->url(fn(SubmittelFinalReportModel $record) => Storage::url($record->file . '?download=1'))
                    // ->downloadable()
                    ->icon('heroicon-o-document')
                    ->color('lime')
                    ->openUrlInNewTab(),
            ]);
    }
}
