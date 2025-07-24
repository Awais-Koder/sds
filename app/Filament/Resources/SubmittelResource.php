<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SubmittelExporter;
use App\Filament\Resources\SubmittelResource\Pages;
use App\Filament\Resources\SubmittelResource\RelationManagers;
use App\Mail\SendNotificationMail;
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
use App\Models\Email;
use App\Models\Setting;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Mail;

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
                        RichEditor::make('name')
                            ->default(Setting::first()?->project_name ?? '')
                            ->required()
                            ->placeholder('Enter the name of the submittel'),
                        Forms\Components\TextInput::make('ref_no')
                            ->required()
                            ->placeholder('Enter the reference number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cycle')
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
                        Forms\Components\Toggle::make('soft_copy')
                            ->reactive(),
                        Forms\Components\DateTimePicker::make('date')
                            ->placeholder('Select the date of the submittel')
                            ->closeOnDateSelection(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->label('Select status')
                            ->reactive()
                            ->visible(fn() => Auth::user()->hasRole(['super_admin', 'actioner', 'editor']))
                            ->options(function () {
                                $user = Auth::user();
                                if ($user->hasRole('editor')) {
                                    return [
                                        'submitted' => 'Submitted',
                                        'draft' => 'Draft',
                                    ];
                                } elseif ($user->hasRole('super_admin')) {
                                    return [
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                        'approved_as_noted' => 'Approved as noted',
                                        'revise_resubmit_as_noted' => 'Revise resubmit as noted',
                                        'rejected' => 'Rejected',
                                        'draft' => 'Draft',
                                    ];
                                } elseif ($user->hasRole('actioner')) {
                                    return [
                                        'approved' => 'Approved',
                                        'approved_as_noted' => 'Approved as noted',
                                        'revise_resubmit_as_noted' => 'Revise resubmit as noted',
                                        'rejected' => 'Rejected',
                                    ];
                                }
                            })->default('approved'),
                        // Forms\Components\Textarea::make('comments')
                        //     ->reactive()
                        //     ->placeholder('Enter comments here')
                        //     ->rows(10),
                        Forms\Components\FileUpload::make('submittel_file')
                            ->label('Submittel File')
                            ->reactive()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/x-pdf',
                                'application/octet-stream', // some scanners
                                '.pdf'
                            ])
                            ->preserveFilenames(),
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
                                    ->preserveFilenames()
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/x-pdf',
                                        'application/octet-stream', // some scanners
                                        '.pdf'
                                    ]),
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
                                // Forms\Components\Select::make('status')
                                //     ->reactive()

                                //     ->options(function () {
                                //         $user = Auth::user();
                                //         if ($user->hasRole('editor')) {
                                //             return [
                                //                 'submitted' => 'Submitted',
                                //                 'draft' => 'Draft',
                                //             ];
                                //         } elseif ($user->hasRole('super_admin')) {
                                //             return [
                                //                 'submitted' => 'Submitted',
                                //                 'under_review' => 'Under review',
                                //                 'revise_and_resubmit' => 'Revise and resubmit',
                                //                 'draft' => 'Draft',
                                //             ];
                                //         } else {
                                //             return [];
                                //         }
                                //     })
                                //     ->default(fn($get) => $get('../../status') == 'draft' ? 'draft' : null),
                                Forms\Components\TextInput::make('cycle')
                                    ->label('Revision')
                                    ->reactive()
                                    ->visible(fn($get) => $get('../../re_submittel') === true)
                                    ->placeholder('Enter the cycle number')
                                    ->minValue(0)
                                    ->default(0)
                                    ->numeric(),
                            ])

                            ->addActionLabel('Add drawing')
                            ->cloneable()
                            ->columns(5)
                            ->collapsible()
                            ->defaultItems(1)
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                                $data['submitted_by'] = auth()->id();
                                $data['submitted_time'] = now();
                                $data['status'] = 'submitted';
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                $data['submitted_by'] = auth()->id();
                                $data['submitted_time'] = now();
                                $data['status'] = 'submitted';
                                return $data;
                            }),
                        Forms\Components\FileUpload::make('soft_copy_file')
                            ->label('Soft Copy (zip or rar)')
                            ->reactive()
                            ->required(fn($get) => $get('soft_copy'))
                            ->acceptedFileTypes(['application/zip', 'application/x-rar-compressed', '.zip', '.rar'])
                            ->preserveFilenames(),
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
                    ->label('Created By')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('name')
                //     ->searchable(),
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
                Tables\Columns\IconColumn::make('sent_to_actioner')
                    ->boolean(),
                Tables\Columns\TextColumn::make('date')
                    ->dateTime('d M Y, h:i A')
                    // ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cycle')
                    ->label('Revision')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'approved_as_noted' => 'lime',
                        'revise_resubmit_as_noted' => 'teal',
                        'rejected' => 'danger',
                        'draft' => 'zinc',
                    })
                    ->formatStateUsing(function (string $state): string {
                        return str_replace('_', ' ', ucfirst(strtolower($state)));
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
                Filter::make('send_by_dc_to_actioner_range')
                    ->label("Received At")
                    ->form([
                        DatePicker::make('from')->label("From date"),
                        DatePicker::make('until')->label("To date")
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
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('teal'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('Pdf')
                        ->color('success')
                        ->url(fn(Submittel $record) => route('download.pdf', $record->id))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('Download Files')
                        ->url(fn(Submittel $record) => route('download.submittel.files', $record->id))
                        ->icon('heroicon-o-document')
                        ->color('lime')
                        ->openUrlInNewTab(),
                    Tables\Actions\ViewAction::make()
                        ->color('primary'),

                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(Auth::user()->hasRole(['super_admin', 'editor'])),
                    ExportBulkAction::make()
                        ->label('Report')
                        ->color('primary')
                        ->exporter(SubmittelExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                            ExportFormat::Csv,
                        ]),

                    Tables\Actions\BulkAction::make('sendToActioner')
                        ->label('Send To Actioner')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $ids = $records->modelKeys();
                            Submittel::whereIn('id', $ids)->update([
                                'sent_to_actioner' => 1,
                                'send_by_dc_to_actioner' => now(),
                            ]);
                        })
                        ->after(function (Collection $records) {
                            // send emails
                            foreach ($records as $submittel) {
                                foreach (Email::all() as $email) {
                                    Mail::to($email)->queue(new SendNotificationMail($submittel->ref_no));
                                }
                            }

                            Notification::make('Submittle(s) Dispatched')
                                ->title('Submittle(s) Dispatched')
                                ->body('The selected submittle(s) have been successfully dispatched to the actioner.')
                                ->success()
                                ->send();
                        })

                        ->requiresConfirmation()
                        ->visible(fn() => Auth::user()->hasRole(['super_admin', 'dc']))
                        ->deselectRecordsAfterCompletion(),
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
            'view' => Pages\ViewSubmittel::route('/{record}'),
            'edit' => Pages\EditSubmittel::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        if (Auth::check() && Auth::user()->hasRole('editor')) {
            $query->where('submitted_by', Auth::id());
        }
        if (Auth::check() && Auth::user()->hasRole('dc')) {
            $query->where('status', 'submitted');
        }

        return $query;
    }
}
