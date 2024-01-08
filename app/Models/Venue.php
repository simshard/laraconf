<?php

namespace App\Models;

use App\Enums\Region;
use App\Models\Conference;
use Forms\Components\Select;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;#

class Venue extends Model
{
    use HasFactory;

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
                ->enum(Region::class )
                ->options(Region::class),

        ];

    }

}
