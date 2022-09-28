<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $phone = rand(0, 999).'-'.rand(0, 999).'-0000';
        $phone = sprintf('%s%04d', substr($phone, 0, -4), rand(0, 9999));
        return [
            'name'  => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'photo' => $this->faker->image(public_path('storage/images'), 300, 300, null, true, null, false),
            'phone' => $phone,
            'password' => Hash::make('password'), // Default password ->  password
            'remember_token'    => Str::random(10),
            'email_verified_at' => now()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
