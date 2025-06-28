<?php

use App\Http\Controllers\PdfDownloadController;
use App\Http\Controllers\SubmittelFilesController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home')->middleware('personal-verify');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'personal-verify'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('download/pdf/{id}', [PdfDownloadController::class, 'downloadPdf'])->name('download.pdf');
    Route::get('download/submittel/files/{id}', [SubmittelFilesController::class, 'downloadSubmittelFiles'])->name('download.submittel.files');
});


require __DIR__ . '/auth.php';
