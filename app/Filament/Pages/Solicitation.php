<?php

namespace App\Filament\Pages;

use Filament\Pages\SimplePage;

class Solicitation extends SimplePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.solicitation';
    protected static ?string $title = 'Solicitar';
}
