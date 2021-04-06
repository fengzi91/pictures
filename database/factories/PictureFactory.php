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
        $width = $this->faker->numberBetween(500, 1000);
        $height = $this->faker->numberBetween(100, 1000);
        $image = $this->faker->imageUrl($width, $height);
        $image = str_replace('https://via.placeholder.com/', 'http://localhost:8088/', $image);
        $image = str_replace('.png', '', $image);
        return [
            'title' => $this->faker->sentence,
            'url' => $image,
            'width' => $width,
            'height' => $height,
            'uuid' => $this->faker->uuid,
        ];
    }
}
