<?php

namespace Mozartdigital\FilamentBlog\Models;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Mozartdigital\FilamentBlog\Database\Factories\PostFactory;
use Mozartdigital\FilamentBlog\Enums\PostStatus;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'body',
        'status',
        'published_at',
        'scheduled_for',
        'cover_photo_path',
        'photo_alt_text',
        'user_id',
    ];

    protected $dates = [
        'scheduled_for',
    ];

    protected $with = ['categories','tags','user','seoDetail'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'status' => PostStatus::class,
        'user_id' => 'integer',
    ];

    protected static function newFactory()
    {
        return new PostFactory;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, config('filamentblog.tables.prefix').'category_'.config('filamentblog.tables.prefix').'post');
    }

    public function comments(): hasmany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, config('filamentblog.tables.prefix').'post_'.config('filamentblog.tables.prefix').'tag');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('filamentblog.user.model'), config('filamentblog.user.foreign_key'));
    }

    public function seoDetail()
    {
        return $this->hasOne(SeoDetail::class);
    }

    public function isNotPublished()
    {
        return ! $this->isStatusPublished();
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('status', PostStatus::PUBLISHED)->latest('published_at');
    }

    public function scopeScheduled(Builder $query)
    {
        return $query->where('status', PostStatus::SCHEDULED)->latest('scheduled_for');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', PostStatus::PENDING)->latest('created_at');
    }

    public function formattedPublishedDate()
    {
        return $this->published_at?->format('d M Y');
    }

    public function isScheduled()
    {
        return $this->status === PostStatus::SCHEDULED;
    }

    public function isStatusPublished()
    {
        return $this->status === PostStatus::PUBLISHED;
    }

    public function scopeFilter($query, array $filters)
    { 
        if ( $filters['search']??false) $filters['search'] = str_replace(' ', '%', $filters['search']);

        $query->when($filters['search']??false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query->where('title','like','%'.$search.'%')
                      ->orWhere('sub_title','like','%'.$search.'%'))
                );

        $query->when($filters['category']??false,fn ($query, $category) =>
                $query->whereHas('categories',fn ($query) =>
                $query->where('slug',$category)));
                      
        $query->when($filters['tag']??false,fn ($query, $tag) =>
                $query->whereHas('tags',fn ($query) =>
                $query->where('slug',$tag)));
    }

    public function relatedPosts($take = 3)
    {
        return $this->whereHas('categories', function ($query) {
            $query->whereIn(config('filamentblog.tables.prefix').'categories.id', $this->categories->pluck('id'))
                ->whereNotIn(config('filamentblog.tables.prefix').'posts.id', [$this->id]);
        })->published()->with('user')->take($take)->get();
    }

    protected function getFeaturePhotoAttribute()
    {
        if (Str::startsWith($this->cover_photo_path, 'http')) {
            return $this->cover_photo_path;
        }
        return asset('storage/'.$this->cover_photo_path);
    }

    public static function getForm()
    {
        return [
            Section::make('Détais de l\'article')
                ->schema([
                    Fieldset::make('Titre')
                        ->schema([
                            Select::make('category_id')
                                ->label('Catégories')
                                ->multiple()
                                ->preload()
                                ->createOptionForm(Category::getForm())
                                ->searchable()
                                ->relationship('categories', 'name')
                                ->columnSpanFull(),

                            TextInput::make('title')
                                ->label('Titre')
                                ->live(true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                    'slug',
                                    Str::slug($state)
                                ))
                                ->required()
                                ->unique(config('filamentblog.tables.prefix').'posts', 'title', null, 'id')
                                ->maxLength(255),

                            TextInput::make('slug')
                                ->maxLength(255),

                            Textarea::make('sub_title')
                                ->label('Sous-titre')
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Select::make('tag_id')
                                ->multiple()
                                ->preload()
                                ->createOptionForm(Tag::getForm())
                                ->searchable()
                                ->relationship('tags', 'name')
                                ->columnSpanFull(),
                        ]),
                    RichEditor::make('body')
                        ->label('Contenu')
                        ->extraInputAttributes(['style' => 'max-height: 30rem; min-height: 24rem'])
                        ->required()
                        ->columnSpanFull(),
                    Fieldset::make('Image de couverture')
                        ->schema([
                            FileUpload::make('cover_photo_path')
                                ->label('Image')
                                ->directory('/blog-feature-images')
                                ->hint('La taille de l\'image recommandé est de 1920x1004')
                                ->image()
                                ->preserveFilenames()
                                ->imageEditor()
                                ->maxSize(1024 * 5)
                                ->required(),
                            TextInput::make('photo_alt_text')->label('Balise ALT')->required(),
                        ])->columns(1),

                    Fieldset::make('Status')
                        ->schema([

                            ToggleButtons::make('status')
                                ->live()
                                ->inline()
                                ->options(PostStatus::class)
                                ->required(),

                            DateTimePicker::make('scheduled_for')
                                ->label('Programmé pour le')
                                ->visible(function ($get) {
                                    return $get('status') === PostStatus::SCHEDULED->value;
                                })
                                ->required(function ($get) {
                                    return $get('status') === PostStatus::SCHEDULED->value;
                                })
                                ->minDate(now()->addMinutes(5))
                                ->native(false),
                        ]),

                ]),
        ];
    }

    public function getTable()
    {
        return config('filamentblog.tables.prefix').'posts';
    }
}
