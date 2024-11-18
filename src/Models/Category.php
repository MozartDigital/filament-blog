<?php

namespace Mozartdigital\FilamentBlog\Models;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Mozartdigital\FilamentBlog\Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Filament\Forms\Components\ColorPicker;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, config('filamentblog.tables.prefix').'category_'.config('filamentblog.tables.prefix').'post');
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->label('Nom')
                ->live(true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {

                    $set('slug', Str::slug($state));
                })
                ->unique(config('filamentblog.tables.prefix').'categories', 'name', null, 'id')
                ->required()
                ->maxLength(155),

            TextInput::make('slug')
                ->unique(config('filamentblog.tables.prefix').'categories', 'slug', null, 'id')
                ->readOnly()
                ->maxLength(255),

            ColorPicker::make('color')
                ->label('Couleur')
                ->hsl()
                ->required()
                ->default('#030070'),
        ];
    }

    protected static function newFactory()
    {
        return new CategoryFactory();
    }

    public function getTable()
    {
        return config('filamentblog.tables.prefix') . 'categories';
    }
}
