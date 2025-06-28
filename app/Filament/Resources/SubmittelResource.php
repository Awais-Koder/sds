<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmittelResource\Pages;
use App\Filament\Resources\SubmittelResource\RelationManagers;
use App\Models\Submittel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Factories\Relationship;

use function Livewire\Volt\placeholder;

class SubmittelResource extends Resource
{
    protected static ?string $model = Submittel::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Submittel Details')
                    ->schema([
                        Forms\Components\Select::make('parent_submittel_id')
                            ->searchable()
                            ->rule('required')
                            ->required()
                            ->native(false)
                            ->preload(5)
                            ->relationship('parent', 'ref_no')
                            ->reactive()
                            ->visible(fn($get) => $get('re_submittel'))
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $parent = \App\Models\Submittel::find($state);
                                    if ($parent) {
                                        $set('cycle', $parent->cycle + 1);
                                    }
                                } else {
                                    $set('cycle', null); // clear if deselected
                                }
                            })
                            ->label('Re Submittel of'),
                        Forms\Components\Textarea::make('name')
                            ->required()
                            ->placeholder('Enter the name of the submittel'),
                        Forms\Components\TextInput::make('ref_no')
                            ->required()
                            ->placeholder('Enter the reference number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cycle')
                            // ->default(fn($record) => $record('cycle'))
                            ->placeholder('Enter the cycle number')
                            ->minValue(0)
                            ->default(0)
                            ->visible(fn($get) => $get('re_submittel'))
                            ->numeric(),
                        Forms\Components\Toggle::make('new_submittel')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === true) {
                                    $set('re_submittel', false);
                                }
                            })
                            ->default(true),
                        Forms\Components\Toggle::make('re_submittel')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === true) {
                                    $set('new_submittel', false);
                                }
                            }),
                        Forms\Components\Toggle::make('additional_copies'),
                        Forms\Components\Toggle::make('soft_copy'),
                        Forms\Components\DateTimePicker::make('date')
                            ->placeholder('Select the date of the submittel')
                            ->columnSpanFull()
                            ->closeOnDateSelection(),

                    ])
                    ->collapsible()
                    ->columns(2),
                Section::make('Shop Drawings Details')
                    ->schema([
                        Repeater::make('Shop Drawings')
                            ->relationship('outgoings')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->native(false)
                                    ->searchable()
                                    ->preload(5)
                                    ->label('Select Category')
                                    ->options(fn() => Category::pluck('name', 'id'))
                                    ->required(),
                                Forms\Components\FileUpload::make('file')
                                    ->label('Upload File')
                                    ->imageEditor()
                                    ->required()
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->maxSize(10240), // 10 MB,
                                Forms\Components\TextInput::make('sds_no')
                                    ->placeholder('Enter SDS Number')
                                    ->label('SDS Number')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('no_of_copies')
                                    ->placeholder('No of Copies')
                                    ->numeric()
                                    ->label('No of Copies'),
                                Forms\Components\TextInput::make('dwg_no')
                                    ->placeholder('Enter Drawing Number')
                                    ->label('Drawing Number')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('description')
                                    ->placeholder('Enter a brief description')
                                    ->label('Description ')
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
                                    ->reactive()
                                    ->visible(fn($get) => $get('../../re_submittel') === true)
                                    ->placeholder('Enter the cycle number')
                                    ->minValue(0)
                                    ->default(0)
                                    ->numeric(),
                            ])
                            ->addActionLabel('Add drawing')
                            ->cloneable()
                            ->columns(7)
                            ->collapsible()
                            ->defaultItems(1),
                    ])
                    ->columnSpanFull()
                    ->collapsible()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ref_no')
                    ->searchable(),
                Tables\Columns\IconColumn::make('new_submittel')
                    ->boolean(),
                Tables\Columns\IconColumn::make('re_submittel')
                    ->boolean(),
                Tables\Columns\IconColumn::make('additional_copies')
                    ->boolean(),
                Tables\Columns\IconColumn::make('soft_copy')
                    ->boolean(),
                Tables\Columns\TextColumn::make('date')
                    ->dateTime('d M Y, h:i A')
                    // ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cycle')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'submitted' => 'warning',
                        'under_review' => 'gray',
                        'rejected' => 'danger',
                    }),
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
                Tables\Actions\EditAction::make()
                    ->color('zinc'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Pdf')
                    ->color('success')
                    ->url(fn(Submittel $record) => route('download.pdf', $record->id))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('Download Files')
                    ->url(fn(Submittel $record) => route('download.submittel.files', $record->id))
                    ->icon('heroicon-o-document')
                    ->color('gray')
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListSubmittels::route('/'),
            'create' => Pages\CreateSubmittel::route('/create'),
            'edit' => Pages\EditSubmittel::route('/{record}/edit'),
        ];
    }
}
