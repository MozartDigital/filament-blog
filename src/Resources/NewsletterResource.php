<?php

namespace Firefly\FilamentBlog\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Firefly\FilamentBlog\Models\NewsLetter;

class NewsletterResource extends Resource
{
    protected static ?string $model = NewsLetter::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $activeNavigationIcon = 'heroicon-s-newspaper';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $modelLabel = 'Newsletter';

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                Forms\Components\Toggle::make('subscribed')
                    ->default(true)
                    ->required()->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('subscribed')
                    ->label('Abonné'),
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
            'index' => \Firefly\FilamentBlog\Resources\NewsletterResource\Pages\ListNewsletters::route('/'),
            'create' => \Firefly\FilamentBlog\Resources\NewsletterResource\Pages\CreateNewsletter::route('/create'),
            // 'edit' => \Firefly\FilamentBlog\Resources\NewsletterResource\Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }
}
