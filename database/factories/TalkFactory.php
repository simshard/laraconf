<?php

namespace Database\Factories;

use App\Models\Speaker;
use App\Models\Talk;
use Illuminate\Database\Eloquent\Factories\Factory;

class TalkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Talk::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->bs(),
            'abstract' => $this->faker->realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2),
            'speaker_id' => Speaker::factory(),
        ];
    }
}
