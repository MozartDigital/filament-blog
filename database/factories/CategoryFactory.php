<?php

namespace Mozartdigital\FilamentBlog\Database\Factories;

use Mozartdigital\FilamentBlog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $name = fake()->words(3,true),
            'slug' => Str::slug($name),
            'color' => fake()->safeHexColor(),
        ];
    }
}
