<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RmdProfile;
use App\Models\RmdBibleReflection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Testing\AssertableInertia as Assert;

class RmdReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_rmd_report_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('rmd-report.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('RmdReport/Index')
            ->has('reports.data')
            ->has('filters')
        );
    }

    public function test_export_analytics_works()
    {
        Excel::fake();
        
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'role' => 'participant',
            'date_of_birth' => now()->subYears(15) // Ensure age > 12
        ]);
        
        $response = $this->actingAs($admin)->get(route('rmd-report.export.analytics'));
        
        $response->assertStatus(200);
        
        Excel::assertDownloaded('rmd_analytics_' . date('Y-m-d_H-i') . '.xlsx');
    }

    public function test_report_shows_participant_summary()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create([
            'role' => 'participant',
            'date_of_birth' => now()->subYears(15)
        ]);
        
        // Create RmdProfile (Filled)
        RmdProfile::create([
            'user_id' => $participant->id,
        ]);
        
        // Create RmdBibleReflection (Filled)
        RmdBibleReflection::create([
            'user_id' => $participant->id,
        ]);

        // Report should show 1 row for the participant with summary of 2 filled modules
        
        $response = $this->actingAs($admin)->get(route('rmd-report.index'));
        
        $response->assertInertia(fn (Assert $page) => $page
            ->component('RmdReport/Index')
            ->has('reports.data', 1)
            ->where('reports.data.0.user_id', $participant->id)
            ->where('reports.data.0.filled_modules_count', 2)
        );
    }

    public function test_export_excel_works()
    {
        Excel::fake();
        
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'participant']);
        
        $response = $this->actingAs($admin)->get(route('rmd-report.export.excel'));
        
        $response->assertStatus(200);
        
        Excel::assertDownloaded('laporan_rmd_' . date('Y-m-d_H-i') . '.xlsx');
    }

    public function test_export_pdf_works()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'participant']);
        
        $response = $this->actingAs($admin)->get(route('rmd-report.export.pdf'));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
