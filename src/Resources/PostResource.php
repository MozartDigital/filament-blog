<?php

namespace Firefly\FilamentBlog\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Firefly\FilamentBlog\Enums\PostStatus;
use Firefly\FilamentBlog\Models\Post;
use Firefly\FilamentBlog\Resources\PostResource\Pages\EditPost;
use Firefly\FilamentBlog\Resources\PostResource\Pages\ManaePostSeoDetail;
use Firefly\FilamentBlog\Resources\PostResource\Pages\ManagePostComments;
use Firefly\FilamentBlog\Resources\PostResource\Pages\ViewPost;
use Firefly\FilamentBlog\Resources\PostResource\Widgets\BlogPostPublishedChart;
use Firefly\FilamentBlog\Tables\Columns\UserPhotoName;
use Illuminate\Support\Str;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-minus';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-minus';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $modelLabel = 'Articles';

    protected static ?string $navigationLabel = 'Articles';

    protected static ?string $recordTitleAttribute = 'Tître';

    protected static ?int $navigationSort = 3;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return strval(Post::where('status', PostStatus::PUBLISHED)->count());
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Articles publiés';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Post::where('status', PostStatus::PUBLISHED)->count() > 5 ? 'success' : 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Post::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->description(function (Post $record) {
                        return Str::limit($record->sub_title, 40);
                    })
                    ->searchable()->limit(20),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),
                Tables\Columns\ImageColumn::make('cover_photo_path')->label('Image de couverture'),

                UserPhotoName::make('user')
                    ->label('Auteur'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
            ->filters([
                // Tables\Filters\SelectFilter::make('user')
                //     ->relationship('user', config('filamentblog.user.columns.name'))
                //     ->searchable()
                //     ->preload()
                //     ->multiple(),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->multiple()
                    ->options(PostStatus::class),
                DateRangeFilter::make('created_at')
                    ->label('Créé le'),
                DateRangeFilter::make('expire_at')
                    ->label('Expire le'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Article')
                ->schema([
                    Fieldset::make('Général')
                        ->schema([
                            TextEntry::make('title')->label('Titre'),
                            TextEntry::make('slug'),
                            TextEntry::make('sub_title')->label('Sous-titre'),
                        ]),
                    Fieldset::make('Information de publication')
                        ->schema([
                            TextEntry::make('status')
                                ->badge()->color(function ($state) {
                                    return $state->getColor();
                                }),
                            TextEntry::make('published_at')
                            ->label('Publié le')
                            ->visible(function (Post $record) {
                                return $record->status === PostStatus::PUBLISHED;
                            }),

                            TextEntry::make('scheduled_for')
                            ->label('Programmé pour')
                            ->visible(function (Post $record) {
                                return $record->status === PostStatus::SCHEDULED;
                            }),
                        ]),
                    Fieldset::make('Description')
                        ->schema([
                            TextEntry::make('body')
                                ->label('Contenu')
                                ->html()
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPost::class,
            ManaePostSeoDetail::class,
            EditPost::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //            \Firefly\FilamentBlog\Resources\PostResource\RelationManagers\SeoDetailRelationManager::class,
            //            \Firefly\FilamentBlog\Resources\PostResource\RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BlogPostPublishedChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Firefly\FilamentBlog\Resources\PostResource\Pages\ListPosts::route('/'),
            'create' => \Firefly\FilamentBlog\Resources\PostResource\Pages\CreatePost::route('/create'),
            'edit' => \Firefly\FilamentBlog\Resources\PostResource\Pages\EditPost::route('/{record}/edit'),
            'view' => \Firefly\FilamentBlog\Resources\PostResource\Pages\ViewPost::route('/{record}'),
            'comments' => \Firefly\FilamentBlog\Resources\PostResource\Pages\ManagePostComments::route('/{record}/comments'),
            'seoDetail' => \Firefly\FilamentBlog\Resources\PostResource\Pages\ManaePostSeoDetail::route('/{record}/seo-details'),
        ];
    }
}
