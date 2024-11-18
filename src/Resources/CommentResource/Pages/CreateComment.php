<?php

namespace Mozartdigital\FilamentBlog\Resources\CommentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Mozartdigital\FilamentBlog\Resources\CommentResource;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;
}
