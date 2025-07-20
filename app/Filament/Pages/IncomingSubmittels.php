<?php

namespace App\Filament\Pages;

use App\Filament\Exports\IncomingExporter;
use App\Models\Submittel;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;

class IncomingSubmittels extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationLabel = 'Incomings';

    protected static string $view = 'filament.pages.incoming-submittels';

    public static function table(Table $table): Table
    {
        return $table
            ->query(Submittel::query()->where('sent_to_actioner', 1))
            ->columns([
                Tables\Columns\TextColumn::make('ref_no')->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                ->searchable()
                    ->label('Submitted By'),
                Tables\Columns\TextColumn::make('send_by_dc_to_actioner')
                    ->dateTime('d M Y, h:i A')
                    ->searchable()
                    ->label('Received at'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => route('filament.app.pages.edit-incoming-submittel', ['record' => $record->getKey()]))
                    ->visible(fn() => Auth::user()->hasRole(['actioner', 'super_admin']))
                    ->label('Action'),
                Tables\Actions\Action::make('Download Files')
                    ->url(fn(Submittel $record) => route('download.submittel.files', $record->id))
                    ->icon('heroicon-o-document')
                    ->color('lime')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label('Report')
                    ->color('primary')
                    ->exporter(IncomingExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                        ExportFormat::Csv,
                    ]),
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
                    })
            ]);
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('recipient_name')
                    ->label('Recipient Name')
                    ->placeholder('Enter recipient\'s name'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            // 'index' => Pages\ListSubmittels::route('/'),
            // 'create' => Pages\CreateSubmittel::route('/create'),
            // 'view' => Pages\ViewSubmittel::route('/{record}'),
            // 'edit' => Pages\Edit::route('/{record}/edit'),
            'edit' => EditIncomingSubmittel::route('/edit-incoming-submittel/{record}'),
        ];
    }
}
