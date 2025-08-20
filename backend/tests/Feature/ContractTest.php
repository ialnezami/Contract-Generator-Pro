<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\User;
use App\Models\ContractVariable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ContractTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and authenticate
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
        
        // Create a template
        $this->template = ContractTemplate::factory()->create([
            'user_id' => $this->user->id,
            'is_public' => true,
        ]);
    }

    /** @test */
    public function user_can_create_a_contract()
    {
        $contractData = [
            'title' => 'Test Contract',
            'description' => 'A test contract for testing purposes',
            'template_id' => $this->template->id,
            'status' => 'draft',
            'variables' => [
                [
                    'name' => 'client_name',
                    'type' => 'text',
                    'value' => 'John Doe',
                ],
                [
                    'name' => 'contract_amount',
                    'type' => 'currency',
                    'value' => '5000.00',
                ],
            ],
        ];

        $response = $this->postJson('/api/contracts', $contractData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'template_id',
                        'user_id',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('contracts', [
            'title' => 'Test Contract',
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
        ]);
    }

    /** @test */
    public function user_can_view_their_contracts()
    {
        // Create some contracts for the user
        Contract::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
        ]);

        $response = $this->getJson('/api/contracts');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'title',
                                'status',
                                'created_at',
                            ],
                        ],
                        'current_page',
                        'per_page',
                        'total',
                    ],
                ]);

        $response->assertJsonCount(3, 'data.data');
    }

    /** @test */
    public function user_can_view_a_specific_contract()
    {
        $contract = Contract::factory()->create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
        ]);

        $response = $this->getJson("/api/contracts/{$contract->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'template_id',
                        'user_id',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function user_can_update_their_contract()
    {
        $contract = Contract::factory()->create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
        ]);

        $updateData = [
            'title' => 'Updated Contract Title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/contracts/{$contract->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Resource updated successfully',
                ]);

        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'title' => 'Updated Contract Title',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function user_can_delete_their_contract()
    {
        $contract = Contract::factory()->create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
        ]);

        $response = $this->deleteJson("/api/contracts/{$contract->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Resource deleted successfully',
                ]);

        $this->assertDatabaseMissing('contracts', ['id' => $contract->id]);
    }

    /** @test */
    public function user_cannot_access_other_users_contracts()
    {
        $otherUser = User::factory()->create();
        $contract = Contract::factory()->create([
            'user_id' => $otherUser->id,
            'template_id' => $this->template->id,
        ]);

        $response = $this->getJson("/api/contracts/{$contract->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_get_contract_statistics()
    {
        // Create contracts with different statuses
        Contract::factory()->create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'status' => 'active',
        ]);

        Contract::factory()->create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/contracts/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'total_contracts',
                        'active_contracts',
                        'draft_contracts',
                    ],
                ]);

        $response->assertJson([
            'data' => [
                'total_contracts' => 2,
                'active_contracts' => 1,
                'draft_contracts' => 1,
            ],
        ]);
    }

    /** @test */
    public function contract_creation_validates_required_fields()
    {
        $response = $this->postJson('/api/contracts', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'template_id']);
    }

    /** @test */
    public function contract_creation_validates_template_exists()
    {
        $response = $this->postJson('/api/contracts', [
            'title' => 'Test Contract',
            'template_id' => 99999, // Non-existent template
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['template_id']);
    }
}
