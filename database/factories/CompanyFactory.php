<?php

namespace Database\Factories;

use App\Enums\CompanyVerificationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'tin_number' => $this->faker->numerify('##########'),
            'logo' => $this->faker->imageUrl(200, 200, 'business'),
            'banner' => $this->faker->imageUrl(1200, 400, 'business'),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'address' => $this->faker->address(),
            'okpo' => $this->faker->numerify('########'),
            'working_time' => '09:00-18:00',
            'legal_entity' => $this->faker->company(),
            'search_tags' => implode(',', $this->faker->words(5)),
            'description' => $this->faker->paragraph(3),
            'verification_status' => $this->faker->randomElement(array_column(CompanyVerificationStatus::cases(), 'value')),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function ($company) {
            // Attach a random user as owner
            $user = User::factory()->create();
            $company->users()->attach($user->id, [
                'role' => 'owner',
                'status' => 'active'
            ]);
        });
    }

    /**
     * Indicate that the company is verified.
     */
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'verification_status' => CompanyVerificationStatus::Verified->value,
            ];
        });
    }

    /**
     * Indicate that the company is pending verification.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'verification_status' => CompanyVerificationStatus::Pending->value,
            ];
        });
    }

    /**
     * Indicate that the company is rejected.
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'verification_status' => CompanyVerificationStatus::Rejected->value,
            ];
        });
    }
}
