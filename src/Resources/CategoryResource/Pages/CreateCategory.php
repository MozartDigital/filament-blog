<?php

namespace Mozartdigital\FilamentBlog\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Mozartdigital\FilamentBlog\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    public static ?string $title = 'Création catégorie';

}
