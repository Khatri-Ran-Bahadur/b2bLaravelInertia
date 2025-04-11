<?php

namespace Database\Factories;

use App\Enums\TenderStatusEnum;
use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tender>
 */
class TenderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'tender_category_id' => TenderCategory::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'location' => $this->faker->city(),
            'budget_from' => $this->faker->randomFloat(2, 1000, 5000),
            'budget_to' => $this->faker->randomFloat(2, 5000, 10000),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'status' => TenderStatusEnum::Open->value,
        ];
    }

    /**
     * Indicate that the tender is open.
     */
    public function open()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => TenderStatusEnum::Open->value,
            ];
        });
    }

    /**
     * Indicate that the tender is closed.
     */
    public function closed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => TenderStatusEnum::Closed->value,
            ];
        });
    }

    /**
     * Indicate that the tender is cancelled.
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => TenderStatusEnum::Cancelled->value,
            ];
        });
    }

    /**
     * Indicate that the tender belongs to a specific company.
     */
    public function forCompany(Company $company)
    {
        return $this->state(function (array $attributes) use ($company) {
            return [
                'company_id' => $company->id,
            ];
        });
    }

    /**
     * Indicate that the tender belongs to a specific category.
     */
    public function forCategory(TenderCategory $category)
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'tender_category_id' => $category->id,
            ];
        });
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Tender $tender) {
            // Create tender products
            $tender->tenderProducts()->createMany([
                [
                    'product_name' => $this->faker->word(),
                    'quantity' => $this->faker->numberBetween(1, 100),
                    'unit' => $this->faker->randomElement(['pcs', 'kg', 'm', 'l']),
                ],
                [
                    'product_name' => $this->faker->word(),
                    'quantity' => $this->faker->numberBetween(1, 100),
                    'unit' => $this->faker->randomElement(['pcs', 'kg', 'm', 'l']),
                ],
            ]);
        });
    }
}
