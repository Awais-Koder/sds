<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Pages\Auth\Login;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class EditLogin extends Login
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.edit-login';
    protected ?string $maxWidth = '2xl';

    public function getTitle(): string | Htmlable
    {
        return __('Title');
    }
    public function getHeading(): string | Htmlable
    {
        return new HtmlString('<h1 class="text-2xl">Welcome To National Contracting SDMS Portal</h1>
        <p class="text-md mt-2 border-b">Shop Drawing Management System - Engineering Department</p>');
    }
    public function hasLogo(): bool
    {
        return false;
    }
    // public function getMaxWidth(): MaxWidth | string | null
    // {
    //     return $this->maxWidth;
    // }
}
