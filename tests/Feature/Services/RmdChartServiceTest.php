<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Models\RmdProfile;
use App\Models\RmdBibleReflection;
use App\Services\RmdChartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RmdChartServiceTest extends TestCase
{
    use RefreshDatabase;

    // Test for getAgeDistributionAllParticipants
    public function test_age_distribution_includes_all_participants()
    {
        // 12-14
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(13)]);
        // 15-18
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(16)]);
        // 19+
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(20)]);
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(55)]);

        // < 12 (should be ignored)
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(10)]);
        
        // Non-participant (should be ignored)
        User::factory()->create(['role' => 'admin', 'date_of_birth' => now()->subYears(25)]);

        $service = new RmdChartService();
        $data = $service->getAgeDistributionAllParticipants();

        $this->assertEquals(4, $data['total']);
        
        $labels = $data['labels'];
        $values = $data['datasets'][0]['data'];
        
        // Check ranges
        // Index 0: 12-14
        $this->assertStringContainsString('12-14 Tahun', $labels[0]);
        $this->assertEquals(1, $values[0]);
        
        // Index 1: 15-18
        $this->assertStringContainsString('15-18 Tahun', $labels[1]);
        $this->assertEquals(1, $values[1]);
        
        // Index 2: 19+
        $this->assertStringContainsString('≥ 19 Tahun', $labels[2]);
        $this->assertEquals(2, $values[2]);
    }

    // Test for getRmdParticipationRate
    public function test_participation_rate_counts_filled_vs_not_filled()
    {
        // User 1: Filled RMD, Age 15 (Should count)
        $u1 = User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(15)]);
        RmdProfile::create(['user_id' => $u1->id]);

        // User 2: Not Filled RMD, Age 15 (Should count)
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(15)]);

        // User 3: Admin (should be ignored)
        User::factory()->create(['role' => 'admin', 'date_of_birth' => now()->subYears(25)]);

        // User 4: Filled RMD, Age 10 (Should be ignored because < 12)
        $u4 = User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(10)]);
        RmdProfile::create(['user_id' => $u4->id]);

        // User 5: Not Filled RMD, Age 10 (Should be ignored because < 12)
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(10)]);

        $service = new RmdChartService();
        $data = $service->getRmdParticipationRate();

        // Total should only be 2 (User 1 and User 2)
        $this->assertEquals(2, $data['total']);
        
        // Index 0: Sudah Mengisi (User 1)
        $this->assertEquals(1, $data['datasets'][0]['data'][0]);
        // Index 1: Belum Mengisi (User 2)
        $this->assertEquals(1, $data['datasets'][0]['data'][1]);
    }

    // Test for getRmdFillingProgressDistribution
    public function test_progress_distribution_categorizes_filled_modules_count()
    {
        // User 1: 0 modules, Age 15 (Should count)
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(15)]);

        // User 2: 2 modules (1-3 range), Age 15 (Should count)
        $u2 = User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(15)]);
        RmdProfile::create(['user_id' => $u2->id]);
        RmdBibleReflection::create(['user_id' => $u2->id]);

        // User 3: 0 modules, Age 10 (Should be ignored)
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(10)]);

        // User 4: 2 modules, Age 10 (Should be ignored)
        $u4 = User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(10)]);
        RmdProfile::create(['user_id' => $u4->id]);
        RmdBibleReflection::create(['user_id' => $u4->id]);

        $service = new RmdChartService();
        $data = $service->getRmdFillingProgressDistribution();

        // Should only count User 1 and User 2
        $this->assertEquals(2, $data['total']);
        $this->assertEquals(1, $data['datasets'][0]['data'][0]); // 0 Modul (User 1)
        $this->assertEquals(1, $data['datasets'][0]['data'][1]); // 1-3 Modul (User 2)
    }
    
    public function test_progress_distribution_logic_simple()
    {
        // User 1: 0 modules, Age 15
        User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(15)]);

        // User 2: 1 module (1-3 range), Age 15
        $u2 = User::factory()->create(['role' => 'participant', 'date_of_birth' => now()->subYears(15)]);
        RmdProfile::create(['user_id' => $u2->id]);

        $service = new RmdChartService();
        $data = $service->getRmdFillingProgressDistribution();

        $this->assertEquals(2, $data['total']);
        
        // 0 Modul
        $this->assertEquals(1, $data['datasets'][0]['data'][0]);
        // 1-3 Modul
        $this->assertEquals(1, $data['datasets'][0]['data'][1]);
    }
}
