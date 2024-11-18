<?php

namespace Mozartdigital\FilamentBlog\Components;

use Illuminate\View\Component;

class HeaderCategory extends Component
{
    public function render()
    {
        return view('filament-blog::components.header-category', [
            'categories' => \Mozartdigital\FilamentBlog\Models\Category::all(),
        ]);
    }
}
