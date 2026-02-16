<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RmdMultipleIntelligence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class RmdTheOnlyOneMeeting2Test extends TestCase
{
    use RefreshDatabase;

    public function test_the_only_one_meeting_2_page_is_displayed()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($user)->get(route('rmd.the-only-one-meeting-2'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Rmd/TheOnlyOneMeeting2')
        );
    }

    public function test_the_only_one_meeting_2_data_can_be_saved()
    {
        $user = User::factory()->create(['role' => 'participant']);

        $linguisticChecklist = [0, 2, 5, 8];
        $logicalChecklist = [1, 3, 5, 7, 9];
        $visualChecklist = [0, 1, 4, 6];
        $kinestheticChecklist = [2, 4, 6, 8];

        $response = $this->actingAs($user)->post(route('rmd.the-only-one-meeting-2.store'), [
            'linguistic_checklist' => $linguisticChecklist,
            'logical_mathematical_checklist' => $logicalChecklist,
            'visual_spatial_checklist' => $visualChecklist,
            'kinesthetic_checklist' => $kinestheticChecklist,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('rmd_multiple_intelligences', [
            'user_id' => $user->id,
        ]);

        $entry = RmdMultipleIntelligence::where('user_id', $user->id)->first();
        $this->assertEquals($linguisticChecklist, $entry->linguistic_checklist);
        $this->assertEquals($logicalChecklist, $entry->logical_mathematical_checklist);
        $this->assertEquals($visualChecklist, $entry->visual_spatial_checklist);
        $this->assertEquals($kinestheticChecklist, $entry->kinesthetic_checklist);
    }

    public function test_the_only_one_meeting_2_page_displays_saved_data()
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $linguisticChecklist = [1, 3, 4];
        $logicalChecklist = [0, 2, 4];
        $visualChecklist = [5, 7, 9];
        $kinestheticChecklist = [1, 3, 5];

        RmdMultipleIntelligence::create([
            'user_id' => $user->id,
            'linguistic_checklist' => $linguisticChecklist,
            'logical_mathematical_checklist' => $logicalChecklist,
            'visual_spatial_checklist' => $visualChecklist,
            'kinesthetic_checklist' => $kinestheticChecklist,
        ]);

        $response = $this->actingAs($user)->get(route('rmd.the-only-one-meeting-2'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Rmd/TheOnlyOneMeeting2')
            ->where('multipleIntelligence.linguistic_checklist', $linguisticChecklist)
            ->where('multipleIntelligence.logical_mathematical_checklist', $logicalChecklist)
            ->where('multipleIntelligence.visual_spatial_checklist', $visualChecklist)
            ->where('multipleIntelligence.kinesthetic_checklist', $kinestheticChecklist)
        );
    }
}
