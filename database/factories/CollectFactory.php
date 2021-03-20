<?php

namespace Database\Factories;

use App\Models\Collect;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Collect::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(2),
            'user_id' => User::factory()
        ];
    }
}
