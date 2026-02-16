<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ProfilePhotoRequest;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_upload_photo_directly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('avatar.jpg', 100);

        $response = $this->actingAs($admin)
            ->post(route('admin.profile-photos.upload', $user), [
                'photo' => $file,
            ]);

        $response->assertStatus(302);
        $user->refresh();

        $this->assertNotNull($user->profile_photo_path);
        $this->assertEquals('active', $user->profile_photo_status);
        Storage::disk('public')->assertExists($user->profile_photo_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_upload_photo',
            'user_id' => $admin->id,
            'target_id' => $user->id,
        ]);
    }

    public function test_user_can_request_photo_upload()
    {
        $user = User::factory()->create(['role' => 'participant']);
        $file = UploadedFile::fake()->create('request.png', 100);

        $response = $this->actingAs($user)
            ->post(route('participant.profile-photo.request'), [
                'photo' => $file,
            ]);

        $response->assertStatus(302);
        $user->refresh();

        $this->assertEquals('pending', $user->profile_photo_status);
        $this->assertDatabaseHas('profile_photo_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $request = ProfilePhotoRequest::first();
        Storage::disk('public')->assertExists($request->photo_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_request_photo',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_request_photo_if_already_pending()
    {
        $user = User::factory()->create([
            'role' => 'participant',
            'profile_photo_status' => 'pending'
        ]);
        
        ProfilePhotoRequest::create([
            'user_id' => $user->id,
            'photo_path' => 'temp/path.jpg',
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('second_request.png', 100);

        $response = $this->actingAs($user)
            ->post(route('participant.profile-photo.request'), [
                'photo' => $file,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'You already have a pending photo request. Please wait for admin review.');
        
        // Ensure no new request was created
        $this->assertEquals(1, ProfilePhotoRequest::where('user_id', $user->id)->count());
    }

    public function test_admin_can_approve_photo_request()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $path = UploadedFile::fake()->create('temp.jpg', 100)->store('profile-photo-requests', 'public');
        
        $photoRequest = ProfilePhotoRequest::create([
            'user_id' => $user->id,
            'photo_path' => $path,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.profile-photos.approve', $photoRequest));

        $response->assertStatus(302);
        $photoRequest->refresh();
        $user->refresh();

        $this->assertEquals('approved', $photoRequest->status);
        $this->assertEquals('active', $user->profile_photo_status);
        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_approve_photo',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_reject_photo_request()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $path = UploadedFile::fake()->create('temp.jpg', 100)->store('profile-photo-requests', 'public');
        
        $photoRequest = ProfilePhotoRequest::create([
            'user_id' => $user->id,
            'photo_path' => $path,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.profile-photos.reject', $photoRequest), [
                'reason' => 'Image too blurry',
            ]);

        $response->assertStatus(302);
        $photoRequest->refresh();
        $user->refresh();

        $this->assertEquals('rejected', $photoRequest->status);
        $this->assertEquals('rejected', $user->profile_photo_status);
        $this->assertEquals('Image too blurry', $photoRequest->rejection_reason);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_reject_photo',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_bulk_upload_photos()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['id_number' => 'USR001']);
        $user2 = User::factory()->create(['id_number' => 'USR002']);

        $csvContent = "id_number,photo_name\nUSR001,photo1.jpg\nUSR002,photo2.png";
        $csvFile = UploadedFile::fake()->createWithContent('upload.csv', $csvContent);

        $photo1 = UploadedFile::fake()->create('photo1.jpg', 100);
        $photo2 = UploadedFile::fake()->create('photo2.png', 100);

        $response = $this->actingAs($admin)
            ->post(route('admin.profile-photos.bulk-upload-csv'), [
                'csv_file' => $csvFile,
                'photos' => [$photo1, $photo2],
            ]);

        $response->assertStatus(302);
        $user1->refresh();
        $user2->refresh();

        $this->assertEquals('active', $user1->profile_photo_status);
        $this->assertEquals('active', $user2->profile_photo_status);
        $this->assertNotNull($user1->profile_photo_path);
        $this->assertNotNull($user2->profile_photo_path);

        Storage::disk('public')->assertExists($user1->profile_photo_path);
        Storage::disk('public')->assertExists($user2->profile_photo_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_bulk_upload_photo',
            'target_id' => $user1->id,
        ]);
    }

    public function test_admin_can_bulk_approve_requests()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $users = User::factory()->count(3)->create();
        $requests = [];

        foreach ($users as $user) {
            $path = UploadedFile::fake()->create('temp.jpg', 100)->store('profile-photo-requests', 'public');
            $requests[] = ProfilePhotoRequest::create([
                'user_id' => $user->id,
                'photo_path' => $path,
                'status' => 'pending',
            ]);
        }

        $ids = collect($requests)->pluck('id')->toArray();

        $response = $this->actingAs($admin)
            ->post(route('admin.profile-photos.bulk-approve'), [
                'ids' => $ids,
            ]);

        $response->assertStatus(302);
        
        foreach ($requests as $req) {
            $req->refresh();
            $this->assertEquals('approved', $req->status);
            $this->assertEquals('active', $req->user->profile_photo_status);
        }
    }

    public function test_admin_can_bulk_reject_requests()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $users = User::factory()->count(3)->create();
        $requests = [];

        foreach ($users as $user) {
            $path = UploadedFile::fake()->create('temp.jpg', 100)->store('profile-photo-requests', 'public');
            $requests[] = ProfilePhotoRequest::create([
                'user_id' => $user->id,
                'photo_path' => $path,
                'status' => 'pending',
            ]);
        }

        $ids = collect($requests)->pluck('id')->toArray();

        $response = $this->actingAs($admin)
            ->post(route('admin.profile-photos.bulk-reject'), [
                'ids' => $ids,
                'reason' => 'Multiple rejections',
            ]);

        $response->assertStatus(302);
        
        foreach ($requests as $req) {
            $req->refresh();
            $this->assertEquals('rejected', $req->status);
            $this->assertEquals('rejected', $req->user->profile_photo_status);
            $this->assertEquals('Multiple rejections', $req->rejection_reason);
        }
    }

    public function test_admin_can_export_photo_requests()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create();
        
        $response = $this->actingAs($admin)
            ->get(route('admin.profile-photos.export', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="profile_photo_requests_' . date('Ymd') . '_' . date('His') . '.csv"');
    }
}
