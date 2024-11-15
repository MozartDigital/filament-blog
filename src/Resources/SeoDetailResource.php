<?php

namespace Firefly\FilamentBlog\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Firefly\FilamentBlog\Models\SeoDetail;

class SeoDetailResource extends Resource
{
    protected static ?string $model = SeoDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-magnifying-glass';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $modelLabel = 'SEO';

    protected static ?string $navigationLabel = 'SEO';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(SeoDetail::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('post.title')
                    ->label('Article')
                    ->limit(20),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('keywords')
                    ->label('Mots-clés')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('D d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('D d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Firefly\FilamentBlog\Resources\SeoDetailResource\Pages\ListSeoDetails::route('/'),
            'create' => \Firefly\FilamentBlog\Resources\SeoDetailResource\Pages\CreateSeoDetail::route('/create'),
            // 'edit' => \Firefly\FilamentBlog\Resources\SeoDetailResource\Pages\EditSeoDetail::route('/{record}/edit'),
        ];
    }
}
