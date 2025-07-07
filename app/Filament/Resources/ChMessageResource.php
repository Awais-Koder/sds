<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChMessageResource\Pages;
use App\Filament\Resources\ChMessageResource\RelationManagers;
use App\Models\ChMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ChMessageResource extends Resource
{
    protected static ?string $model = ChMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('from_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('to_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('body')
                    ->maxLength(5000),
                Forms\Components\TextInput::make('attachment')
                    ->maxLength(255),
                Forms\Components\Toggle::make('seen')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('to.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body')
                    ->default('N/A')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('attachment')
                //     ->default('N/A')
                //     ->searchable(),
                Tables\Columns\IconColumn::make('seen')
                    ->boolean(),
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
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->color('success')
                    ->url(function ($record) {
                        $json = json_decode($record->attachment, true);
                        return Storage::url('attachments/' . ($json['new_name'] ?? ''));
                    })
                    ->disabled(function ($record) {
                        $attachment = json_decode($record->attachment, true);
                        return empty($attachment) || empty($attachment['new_name']);
                    })

                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray')
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
            'index' => Pages\ListChMessages::route('/'),
            'create' => Pages\CreateChMessage::route('/create'),
            'edit' => Pages\EditChMessage::route('/{record}/edit'),
        ];
    }
}
