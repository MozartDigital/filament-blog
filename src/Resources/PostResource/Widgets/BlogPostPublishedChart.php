<?php

namespace Firefly\FilamentBlog\Resources\PostResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Firefly\FilamentBlog\Models\Post;

class BlogPostPublishedChart extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            BaseWidget\Stat::make('Article Publié', Post::published()->count()),
            BaseWidget\Stat::make('Article Programmé', Post::scheduled()->count()),
            BaseWidget\Stat::make('Article en attente', Post::pending()->count()),
        ];
    }
}
