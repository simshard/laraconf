<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Enums\Region;
use App\Models\Venue;
use App\Models\Speaker;
use Filament\Forms\Form;
use App\Models\Conference;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Conference Name')
                    ->helperText('The name of the conference')
                    ->default('Laracon Online')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('start_date')
                    ->required()
                    ->native(false),
                Forms\Components\DateTimePicker::make('end_date')
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->hint('The status of the conference')
                    ->hintIcon('heroicon-o-information-circle')
                    ->required(),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->hint('Whether the conference is published')
                    ->hintIcon('heroicon-o-cube')
                    ->default(true),
                Forms\Components\Select::make('region')
                    ->live()
                    ->enum(Region::class)
                    ->options(Region::class),
                Forms\Components\Select::make('venue_id')
                    ->searchable()
                    ->preload()      
                    ->createOptionForm(Venue::getForm())
                    ->editOptionForm(Venue::getForm())
                    ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                        return $query->where('region', $get('region'));
                    }),
                Forms\Components\CheckboxList::make('speakers')
                ->relationship('speakers', 'name')  
                ->options(Speaker::all()->pluck( 'name','id' ))
                ->required(),
            ]);

            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_published')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('venue.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
