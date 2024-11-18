<?php

namespace Mozartdigital\FilamentBlog\Resources\SettingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Mozartdigital\FilamentBlog\Resources\SettingResource;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    public static ?string $title = 'Création des paramètres';

//    protected function beforeCreate(): void
//    {
//        dd($this->data);
//    }
}
