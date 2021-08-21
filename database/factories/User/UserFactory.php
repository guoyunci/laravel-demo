<?php

namespace Database\Factories\User;

use App\Models\User\Address;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->name(),
            'password' => Hash::make('123456'), // password
            'gender' => $this->faker->randomKey([0, 1, 2]),
            'mobile' => $this->faker->phoneNumber,
            'avatar' => $this->faker->imageUrl(),
        ];
    }

    public function addressDefault()
    {
        return $this->state(function () {
            return [];
        })->afterCreating(function ($user) {
            Address::factory()->create([
                'user_id' => $user->id,
                'is_default' => 1
            ]);
        });
    }
}
