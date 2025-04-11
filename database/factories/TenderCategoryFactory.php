<?php

namespace Database\Factories;

use App\Models\TenderCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TenderCategory>
 */
class TenderCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'status' => 'active',
            'parent_id' => null,
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    /**
     * Indicate that the category has a parent.
     */
    public function withParent()
    {
        return $this->state(function (array $attributes) {
            $parent = TenderCategory::factory()->create();
            return [
                'parent_id' => $parent->id,
            ];
        });
    }

    /**
     * Indicate that the category is a child of a specific parent.
     */
    public function childOf(TenderCategory $parent)
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'parent_id' => $parent->id,
            ];
        });
    }
}
