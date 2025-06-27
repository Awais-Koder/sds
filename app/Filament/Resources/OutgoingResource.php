<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutgoingResource\Pages;
use App\Filament\Resources\OutgoingResource\RelationManagers;
use App\Models\Outgoing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class OutgoingResource extends Resource
{
    protected static ?string $model = Outgoing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->label('File Name'),
                Forms\Components\TextInput::make('sds_no')
                    ->placeholder('Enter SDS Number')
                    ->maxLength(255),
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
                        }

                        return [
                            'submitted' => 'Submitted',
                            'under_review' => 'Under review',
                            'revise_and_resubmit' => 'Revise and resubmit',
                        ];
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
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('submittel.ref_no')
                    ->label('Submittel Ref No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('file')
                    ->label('File Name')
                    ->searchable(),
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
}
