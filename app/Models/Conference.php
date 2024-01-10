<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_published',
        'status',
        'region',
        'venue_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'venue_id' => 'integer',
        'region' => Region::class,
        'is_published' => 'boolean',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            Section::make('Conference Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Conference Name')
                        ->helperText('The name of the conference')
                        ->default('?')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    RichEditor::make('description')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                    DateTimePicker::make('start_date')
                        ->required()
                        ->native(false),
                    DateTimePicker::make('end_date')
                        ->required()
                        ->native(false),
                    Fieldset::make('Status')
                        ->columns(2)
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ])
                                // ->hint('The status of the conference')
                                // ->hintIcon('heroicon-o-information-circle')
                                ->required(),
                            Toggle::make('is_published')
                                ->label('Published')
                                // ->hint('Whether the conference is published')
                                // ->hintIcon('heroicon-o-cube')
                                ->default(true),
                        ]),
                ]),
            Section::make('Conference Location')
                ->columns(2)
                ->schema([
                    Select::make('region')
                        ->live()
                        ->enum(Region::class)
                        ->options(Region::class),

                    Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(Venue::getForm())
                        ->editOptionForm(Venue::getForm())
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                            return $query->where('region', $get('region'));
                        }),
                ]),
            Section::make('Conference Speakers')
                ->columns(2)
                ->schema([
                    CheckboxList::make('speakers')
                        ->columns(2)
                        ->relationship('speakers', 'name')
                        ->options(Speaker::all()->pluck('name', 'id'))
                        ->required(),
                ]),

            Actions::make([
                Action::make('star')
                    ->label('Fill with Faketory Data')
                    ->icon('heroicon-m-star')
                    ->visible(function (string $operation) {
                        if ($operation !== 'create') {
                            return false;
                        }
                        if (! app()->environment('local')) {
                            return false;
                        }

                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),

            ]),

        ];
    }
}
