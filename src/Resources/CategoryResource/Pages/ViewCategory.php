<?php

namespace Firefly\FilamentBlog\Resources\CategoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Firefly\FilamentBlog\Models\Category;
use Firefly\FilamentBlog\Resources\CategoryResource;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    public static ?string $title = 'Détails catégories';

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->slideOver()
                ->form(Category::getForm()),
        ];
    }
}
