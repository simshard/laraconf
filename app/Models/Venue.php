<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Venue extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'city',
        'country',
        'region',
        'postal_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'region' => Region::class,
    ];

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }

    public static function getForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('city')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('country')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('postal_code')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('region')
                ->enum(Region::class)
                ->options(Region::class),
            SpatieMediaLibraryFileUpload::make('images')
                ->collection('venue-images')
                ->multiple()
                ->image(),

        ];

    }
}
