<?php

namespace Mozartdigital\FilamentBlog\Resources\ShareSnippetResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Mozartdigital\FilamentBlog\Resources\ShareSnippetResource;

class EditShareSnippet extends EditRecord
{
    protected static string $resource = ShareSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
