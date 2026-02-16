<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ParticipantTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AuditLog::flushEventListeners();
        parent::tearDown();
    }

    /**
     * Test successful participant creation (Transaction Commit).
     */
    public function test_participant_creation_executes_transaction_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $participantData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'nickname' => 'Johnny',
            'email' => 'john.doe@example.com',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'age' => 24,
            'education' => 'Bachelor',
            'age_group' => 'adult',
            'height' => 180,
            'weight' => 75,
            'communication' => 'email',
        ];

        // Ensure no exception is thrown
        $response = $this->actingAs($admin)->post(route('participants.store'), $participantData);

        $response->assertRedirect(route('participants.index'));
        $response->assertSessionHas('success', 'Participant created successfully.');

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'role' => 'participant',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'CREATE_PARTICIPANT',
            'user_id' => $admin->id,
        ]);
    }

    /**
     * Test successful participant deletion (Transaction Commit).
     */
    public function test_participant_deletion_executes_transaction_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create(['role' => 'participant']);

        // Ensure no exception is thrown
        $response = $this->actingAs($admin)->delete(route('participants.destroy', $participant->id));

        $response->assertRedirect(route('participants.index'));
        $response->assertSessionHas('success', 'Participant deleted successfully.');

        $this->assertDatabaseMissing('users', [
            'id' => $participant->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'DELETE_PARTICIPANT',
            'user_id' => $admin->id,
            'target_id' => $participant->id,
        ]);
    }

    /**
     * Test successful participant status toggle (Transaction Commit).
     */
    public function test_participant_status_toggle_executes_transaction_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create(['role' => 'participant', 'is_active' => true]);

        // Ensure no exception is thrown
        $response = $this->actingAs($admin)->patch(route('participants.toggle-status', $participant->id));

        $response->assertSessionHas('success', 'Participant status updated.');

        $this->assertDatabaseHas('users', [
            'id' => $participant->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'TOGGLE_STATUS_PARTICIPANT',
            'user_id' => $admin->id,
            'target_id' => $participant->id,
        ]);
    }

    /**
     * Test transaction rollback on failure (Transaction Rollback).
     */
    public function test_participant_creation_rolls_back_on_failure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Force AuditLog creation failure to trigger rollback
        AuditLog::creating(function ($auditLog) {
            throw new \Exception('Forced failure for rollback test');
        });

        $participantData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'date_of_birth' => '2000-01-01',
            'gender' => 'female',
            'age' => 24,
            'education' => 'Bachelor',
            'age_group' => 'adult',
            'height' => 170,
            'weight' => 60,
            'communication' => 'email',
        ];

        $response = $this->actingAs($admin)->post(route('participants.store'), $participantData);

        // Should redirect back with error
        $response->assertSessionHas('error');
        
        // Assert user was NOT created due to rollback
        $this->assertDatabaseMissing('users', [
            'email' => 'jane.doe@example.com',
        ]);
    }
}
