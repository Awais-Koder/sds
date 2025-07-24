<?php

namespace App\Filament\Pages;

use App\Mail\SendActionerReplyMail;
use App\Models\Email;
use App\Models\Submittel;
use App\Models\SubmittelFinalReport;
use Filament\Pages\Page;
use Livewire\Attributes\Url;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class EditIncomingSubmittel extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?string $navigationLabel = "Edit Submittel";
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.edit-incoming-submittel';

    #[Url]
    public $record;
    public $data;

    public $submittinData;
    public function mount(): void
    {
        $this->data = Submittel::findOrFail($this->record);
        $this->form->fill($this->data->toArray());
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ref_no')
                    ->rule('required')
                    ->label('Ref No')
                    ->readOnly()
                    ->placeholder('Reference No'),
                Forms\Components\FileUpload::make('file')
                    ->acceptedFileTypes(['application/zip', 'application/x-rar-compressed', 'application/pdf', '.zip', '.rar', '.pdf'])
                    ->maxSize(10240)
                    ->label('File'),
                Forms\Components\TextInput::make('cycle')
                    ->label('Revision')
                    ->placeholder('Revision'),
                Forms\Components\Select::make('status')
                    ->rules([
                        'required',
                        'in:approved,under_review,approved_as_noted,revise_and_resubmit,rejected',
                    ])
                    ->options(function () {
                        return [
                            'approved' => 'Approved',
                            'under_review' => 'Under review',
                            'approved_as_noted' => 'Approved As Noted',
                            'revise_and_resubmit' => 'Revise And Resubmit',
                            'rejected' => 'Rejected',
                        ];
                    }),
                Forms\Components\Textarea::make('comments')
                    ->placeholder('Comments here')
                    ->rows(10)
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->statePath('data');
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->visible(fn() => Auth::user()->hasRole(['actioner', 'super_admin']))
                ->action('save'),
        ];
    }

    public function save()
    {
        // $this->halt();
        $data = $this->form->getState();
        $data['submittel_id'] = $this->record;

        if (SubmittelFinalReport::where('submittel_id', $this->record)->exists()) {
            Notification::make()
                ->title('Record Exists')
                ->body('Record against this submittel already exists.')
                ->danger()
                ->send();
            return;
        }

        SubmittelFinalReport::create($data);

        // update date fields in submittel table

        $submittel = Submittel::findOrFail($this->record);
        $submittel->update([
            'approved_by' => Auth::id(),
            'update_time' => now(),
            'mark_by_actioner' => now(),
        ]);
        foreach (Email::all() as $email) {
            Mail::to($email)->queue(new SendActionerReplyMail($submittel->ref_no));
        }
        Notification::make()
            ->title('Saved Successfully')
            ->success()
            ->send();

        return redirect()->route('filament.app.pages.incoming-submittels');
    }
    // public $data;

    // public function mount()
    // {
    //     $this->data = Submittel::findOrFail($this->record);
    // }
}
