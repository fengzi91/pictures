<?php

namespace Database\Factories;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Factories\Factory;

class PictureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Picture::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'url' => $this->faker->imageUrl(),
            'width' => $this->faker->numberBetween(100, 1000),
            'height' => $this->faker->numberBetween(200, 3000),
            'uuid' => $this->faker->uuid,
        ];
    }
}
