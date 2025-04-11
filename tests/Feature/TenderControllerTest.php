<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TenderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $tenderCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create();

        // Create a company and attach the user as owner
        $this->company = Company::factory()->create();
        $this->company->users()->attach($this->user->id, [
            'role' => 'owner',
            'status' => 'active'
        ]);

        $this->tenderCategory = TenderCategory::factory()->create();

        // Create a personal access client for testing
        $client = \Laravel\Passport\Client::factory()->create([
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);

        // Set the personal access client ID in the database
        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Generate token and authenticate the user
        $token = $this->user->createToken('test-token')->accessToken;
        $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    /** @test */
    public function it_can_create_a_tender_with_images()
    {
        Storage::fake('public');

        $images = [
            UploadedFile::fake()->image('tender1.jpg'),
            UploadedFile::fake()->image('tender2.jpg')
        ];

        $tenderData = [
            'title' => 'Test Tender',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'budget_from' => 1000,
            'budget_to' => 2000,
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'company_id' => $this->company->id,
            'tender_category_id' => $this->tenderCategory->id,
            'tender_products' => [
                [
                    'product_name' => 'Product 1',
                    'quantity' => 10,
                    'unit' => 'pcs'
                ]
            ],
            'images' => $images
        ];

        // Ensure we're authenticated
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/tenders/store', $tenderData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('tender.tender_created')
            ]);

        $this->assertDatabaseHas('tenders', [
            'title' => 'Test Tender',
            'company_id' => $this->company->id
        ]);

        $tender = Tender::first();
        $this->assertEquals(2, $tender->getMedia('tender_images')->count());
    }

    /** @test */
    public function it_can_show_a_tender_with_images()
    {
        $tender = Tender::factory()->create([
            'company_id' => $this->company->id,
            'tender_category_id' => $this->tenderCategory->id
        ]);

        // Add some images
        $tender->addMedia(UploadedFile::fake()->image('test.jpg'))
            ->toMediaCollection('tender_images');

        $response = $this->getJson("/api/tenders/{$tender->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'media' => [
                        '*' => [
                            'id',
                            'url',
                            'thumb'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_tender_with_new_images()
    {
        Storage::fake('public');

        $tender = Tender::factory()->create([
            'company_id' => $this->company->id,
            'tender_category_id' => $this->tenderCategory->id
        ]);

        $newImage = UploadedFile::fake()->image('new_image.jpg');

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'location' => 'Updated Location',
            'budget_from' => 1500,
            'budget_to' => 2500,
            'phone' => '9876543210',
            'email' => 'updated@example.com',
            'tender_category_id' => $this->tenderCategory->id,
            'images' => [$newImage]
        ];

        $response = $this->putJson("/api/tenders/update/{$tender->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('tender.tender_updated')
            ]);

        $this->assertDatabaseHas('tenders', [
            'id' => $tender->id,
            'title' => 'Updated Title'
        ]);

        $tender->refresh();
        $this->assertEquals(1, $tender->getMedia('tender_images')->count());
    }

    /** @test */
    public function it_can_remove_a_specific_image_from_tender()
    {
        $tender = Tender::factory()->create([
            'company_id' => $this->company->id,
            'tender_category_id' => $this->tenderCategory->id
        ]);

        // Add two images
        $media1 = $tender->addMedia(UploadedFile::fake()->image('image1.jpg'))
            ->toMediaCollection('tender_images');
        $media2 = $tender->addMedia(UploadedFile::fake()->image('image2.jpg'))
            ->toMediaCollection('tender_images');

        $response = $this->deleteJson("/api/tenders/{$tender->id}/images/{$media1->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('tender.image_deleted')
            ]);

        $tender->refresh();
        $this->assertEquals(1, $tender->getMedia('tender_images')->count());
        $this->assertNull($tender->getMedia('tender_images')->where('id', $media1->id)->first());
    }

    /** @test */
    public function it_can_delete_a_tender_with_all_its_images()
    {
        Storage::fake('public');

        $tender = Tender::factory()->create([
            'company_id' => $this->company->id,
            'tender_category_id' => $this->tenderCategory->id
        ]);

        // Add some images
        $tender->addMedia(UploadedFile::fake()->image('test1.jpg'))
            ->toMediaCollection('tender_images');
        $tender->addMedia(UploadedFile::fake()->image('test2.jpg'))
            ->toMediaCollection('tender_images');

        $response = $this->deleteJson("/api/tenders/delete/{$tender->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('tender.tender_deleted')
            ]);

        $this->assertDatabaseMissing('tenders', ['id' => $tender->id]);
        $this->assertEquals(0, $tender->getMedia('tender_images')->count());
    }

    /** @test */
    public function it_returns_404_when_trying_to_remove_nonexistent_image()
    {
        $tender = Tender::factory()->create([
            'company_id' => $this->company->id,
            'tender_category_id' => $this->tenderCategory->id
        ]);

        $response = $this->deleteJson("/api/tenders/{$tender->id}/images/999");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => __('tender.image_not_found')
            ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_tender()
    {
        $response = $this->postJson('/api/tenders/store', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'description',
                'location',
                'budget_from',
                'budget_to',
                'phone',
                'email',
                'tender_category_id',
                'tender_products',
                'images'
            ]);
    }

    /** @test */
    public function it_cannot_create_tender_for_unauthorized_company()
    {
        // Create another user and company
        $otherUser = User::factory()->create();
        $otherCompany = Company::factory()->create();
        $otherCompany->users()->attach($otherUser->id, [
            'role' => 'owner',
            'status' => 'active'
        ]);

        $tenderData = [
            'title' => 'Test Tender',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'budget_from' => 1000,
            'budget_to' => 2000,
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'tender_category_id' => $this->tenderCategory->id,
            'company_id' => $otherCompany->id, // Try to create tender for other company
            'tender_products' => [
                [
                    'product_name' => 'Product 1',
                    'quantity' => 10,
                    'unit' => 'pcs'
                ]
            ],
            'images' => [UploadedFile::fake()->image('test.jpg')]
        ];

        $response = $this->postJson('/api/tenders/store', $tenderData);

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function it_cannot_update_tender_for_unauthorized_company()
    {
        // Create another user and company
        $otherUser = User::factory()->create();
        $otherCompany = Company::factory()->create();
        $otherCompany->users()->attach($otherUser->id, [
            'role' => 'owner',
            'status' => 'active'
        ]);

        // Create a tender for the other company
        $tender = Tender::factory()->create([
            'company_id' => $otherCompany->id,
            'tender_category_id' => $this->tenderCategory->id
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'location' => 'Updated Location',
            'budget_from' => 1500,
            'budget_to' => 2500,
            'phone' => '9876543210',
            'email' => 'updated@example.com',
            'tender_category_id' => $this->tenderCategory->id,
        ];

        $response = $this->putJson("/api/tenders/update/{$tender->id}", $updateData);

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function it_cannot_delete_tender_for_unauthorized_company()
    {
        // Create another user and company
        $otherUser = User::factory()->create();
        $otherCompany = Company::factory()->create();
        $otherCompany->users()->attach($otherUser->id, [
            'role' => 'owner',
            'status' => 'active'
        ]);

        // Create a tender for the other company
        $tender = Tender::factory()->create([
            'company_id' => $otherCompany->id,
            'tender_category_id' => $this->tenderCategory->id
        ]);

        $response = $this->deleteJson("/api/tenders/delete/{$tender->id}");

        $response->assertStatus(403); // Forbidden
    }
}
