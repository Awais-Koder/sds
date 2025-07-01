<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutgoingResource\Pages;
use App\Filament\Resources\OutgoingResource\RelationManagers;
use App\Models\Incoming;
use App\Models\Outgoing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\OutgoingExporter;

class OutgoingResource extends Resource
{
    protected static ?string $model = Outgoing::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(5)
                    ->required(),
                Forms\Components\Select::make('submittel_id')
                    ->relationship('submittel', 'ref_no')
                    ->native(false)
                    ->searchable()
                    ->preload(5)
                    ->required(),
                Forms\Components\FileUpload::make('file')
                    ->downloadable()
                    ->label('File Name'),
                Forms\Components\TextInput::make('sds_no')
                    ->placeholder('Enter SDS Number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_of_copies')
                    ->placeholder('No of Copies')
                    ->numeric()
                    ->default(1)
                    ->minValue(0)
                    ->label('No of Copies'),
                Forms\Components\TextInput::make('dwg_no')
                    ->placeholder('Enter Drawing Number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->placeholder('Enter Description')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options(function () {
                        $user = Auth::user();
                        if ($user->hasRole('editor')) {
                            return [
                                'submitted' => 'Submitted',
                            ];
                        } elseif ($user->hasRole('super_admin')) {
                            return [
                                'submitted' => 'Submitted',
                                'under_review' => 'Under review',
                                'revise_and_resubmit' => 'Revise and resubmit',
                            ];
                        } else {
                            return [];
                        }
                    }),
                Forms\Components\TextInput::make('cycle')
                    ->default(0)
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('submittel.ref_no')
                    ->label('Submittel Ref No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sds_no')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dwg_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'submitted' => 'warning',
                        'under_review' => 'gray',
                        'revise_and_resubmit' => 'danger',
                    })
                    ->formatStateUsing(function (string $state): string {
                        return str_replace('_', ' ', ucfirst(strtolower($state)));
                    }),
                Tables\Columns\TextColumn::make('cycle')
                    ->default(0)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(
                [
                    SelectFilter::make('submittel_id')
                        ->label('Filter by Submittel')
                        ->relationship('submittel', 'ref_no') // assuming Submittel has 'title' field
                        ->searchable()
                        ->preload()
                        ->indicator('Submittel')
                        ->placeholder('All Submittels'),
                    SelectFilter::make('category_id')
                        ->label('Filter by Category')
                        ->relationship('category', 'name') // assuming Submittel has 'title' field
                        ->searchable()
                        ->preload()
                        ->placeholder('All Categories'),
                    SelectFilter::make('status')
                        ->label('Filter by Status')
                        ->options([
                            'submitted' => 'Submitted',
                            'revise_and_resubmit' => 'Revise and resubmit',
                            'under_review'  => 'Under review',
                        ])
                        ->searchable()
                        ->preload()
                        ->placeholder('All Statuses'),
                    SelectFilter::make('cycle')
                        ->label('Filter by Cycle')
                        ->options(Outgoing::query()
                            ->select('cycle')
                            ->distinct()
                            ->pluck('cycle', 'cycle')
                            ->toArray())
                        ->searchable()
                        ->preload()
                        ->placeholder('All Statuses')
                ],
                layout: FiltersLayout::AboveContentCollapsible
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Pdf')
                    ->color('success')
                    ->url(fn(Outgoing $record) => Storage::disk('public')->url($record->file))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->visible(Auth::user()->hasRole(['super_admin', 'editor'])),
                    Tables\Actions\BulkAction::make('sendToActioner')
                        ->label('Send To Actioner')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->toArray();

                            $outgoings = Outgoing::whereIn('id', $ids)->get();

                            $incomingData = $outgoings->map(function ($outgoing) {
                                return collect($outgoing->toArray()) // 🔥 RETURN is required here
                                    ->except(['id', 'file_location'])
                                    ->merge([
                                        'status' => 'under_review',
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ])
                                    ->toArray();
                            })->toArray();
                            Incoming::insert($incomingData);
                        })->after(function () {
                            Notification::make('Files Dispatched')
                                ->title('Files Dispatched')
                                ->body('The selected files have been successfully dispatched to the actioner.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn() => Auth::user()->hasRole(['super_admin', 'dc']))
                        ->deselectRecordsAfterCompletion(),

                    ExportBulkAction::make()
                        ->color('primary')
                        ->label('Export')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->exporter(OutgoingExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                            ExportFormat::Csv,
                        ])

                ]),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutgoings::route('/'),
            'create' => Pages\CreateOutgoing::route('/create'),
            'edit' => Pages\EditOutgoing::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        if (Auth::check() && Auth::user()->hasRole('actioner')) {
            $query->where('file_location', 'actioner'); // <-- adjust this string as needed
        }

        return $query;
    }
}
