<?php

namespace App\Filament\Resources\SubmittelResource\Pages;

use App\Filament\Resources\SubmittelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSubmittel extends CreateRecord
{
    protected static string $resource = SubmittelResource::class;

    public $shopDrawingsTemp = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        $this->shopDrawingsTemp = $data['Shop Drawings'] ?? [];
        if (isset($data['new_submittel'])) {
            $data['cycle'] = 0;
        }
        $data['submitted_by'] = Auth::id();
        $data['submitted_time'] = now();
        $data['status'] = 'submitted';
        unset($data['Shop Drawings']);
        return $data;
    }

    protected function afterCreate()
    {
        // dd($this->shopDrawingsTemp);
        // foreach ($this->shopDrawingsTemp as $drawing) {
        //     $this->record->outgoings()->create($drawing);
        //     // Modify status only for incoming
        //     $incomingData = array_merge($drawing, ['status' => 'under_review']);
        //     dd($incomingData);
        //     $this->record->incomings()->create($incomingData);
        // }
    }
}
