<?php

namespace Mozartdigital\FilamentBlog\Components;

use Mozartdigital\FilamentBlog\Models\Setting;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render()
    {
        $setting = Setting::first();

        return view('filament-blog::layouts.app', ['setting' => $setting]);
    }
}
