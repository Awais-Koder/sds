<?php

namespace App\Filament\Resources;

use App\Filament\Exports\IncomingExporter;
use App\Filament\Resources\IncomingResource\Pages;
use App\Filament\Resources\IncomingResource\RelationManagers;
use App\Models\Incoming;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportBulkAction;

class IncomingResource extends Resource
{
    protected static ?string $model = Incoming::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

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
                    ->required()
                    ->reactive()
                    ->options(function () {
                        return [
                            'approved' => 'Approved',
                            'under_review' => 'Under review',
                            'approved_as_noted' => 'Approved As Noted',
                            'revise_and_resubmit' => 'Revise And Resubmit',
                            'rejected' => 'Rejected',
                        ];
                    }),
                Forms\Components\TextInput::make('cycle')
                    ->default(0)
                    ->columnSpanFull()
                    ->numeric(),
                Forms\Components\Textarea::make('comments')
                    ->placeholder('Comments here')
                    ->rows(10)
                    ->visible(fn($get) => $get('status') !== 'approved')
                    ->required(fn($get) => $get('status') !== 'approved')
                    ->reactive()
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approved.name')
                    ->label('Approved By')
                    ->default('N/A')
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
                        'approved' => 'success',
                        'under_review' => 'primary',
                        'approved_as_noted' => 'teal',
                        'revise_and_resubmit' => 'lime',
                        'rejected' => 'danger',
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
                            'approved' => 'Approved',
                            'under_review' => 'Under review',
                            'approved_as_noted' => 'Approved As Noted',
                            'revise_and_resubmit' => 'Revise And Resubmit',
                            'rejected' => 'Rejected',
                        ])
                        ->searchable()
                        ->preload()
                        ->placeholder('All Statuses'),
                    SelectFilter::make('cycle')
                        ->label('Filter by Cycle')
                        ->options(Incoming::query()
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
            ])
            ->headerActions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(Auth::user()->hasRole(['super_admin', 'editor'])),
                    ExportBulkAction::make()
                        ->label('Export')
                        ->color('primary')
                        ->exporter(IncomingExporter::class)
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
            'index' => Pages\ListIncomings::route('/'),
            'create' => Pages\CreateIncoming::route('/create'),
            'edit' => Pages\EditIncoming::route('/{record}/edit'),
        ];
    }
}
