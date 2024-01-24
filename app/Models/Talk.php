<?php

namespace App\Models;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Talk extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'abstract',
        'speaker_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'speaker_id' => 'integer',
        'status' => TalkStatus::class,
        'length' => TalkLength::class,
    ];

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function approve(): void
    {
        $this->status = TalkStatus::APPROVED;

        // email the speaker telling
        $this->save();
    }

    public function reject(): void
    {
        $this->status = TalkStatus::REJECTED;

        // email the speaker telling
        $this->save();
    }

    public static function getForm($speakerId = null): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            Textarea::make('abstract')
                ->required()
                ->maxLength(65535)
                ->columnSpanFull(),
            Select::make('speaker_id')
                ->relationship('speaker', 'name')
                ->required(),
        ];
    }
}
