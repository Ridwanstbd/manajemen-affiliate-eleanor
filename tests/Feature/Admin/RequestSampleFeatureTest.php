<?php

namespace Tests\Feature\Admin;

use App\Models\SampleRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestSampleFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'ADMINISTRATOR']);
    }

    public function test_admin_can_reject_sample_request()
    {
        $sample = SampleRequest::factory()->create(['status' => 'PENDING']);

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-dashboard.request-samples.reject'), [
                             'sample_request_id' => $sample->id,
                             'reject_reason'     => 'Stok habis',
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('sample_requests', [
            'id' => $sample->id,
            'status' => 'REJECTED',
            'reject_reason' => 'Stok habis',
        ]);
    }

    public function test_admin_can_update_resi_and_approve_sample()
    {
        $sample = SampleRequest::factory()->create(['status' => 'PENDING']);

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-dashboard.request-samples.update-resi'), [
                             'sample_request_id' => $sample->id,
                             'courier'           => 'JNE',
                             'tracking_number'   => 'RESI123456789',
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sample_requests', [
            'id' => $sample->id,
            'status' => 'APPROVED',
            'courier' => 'JNE',
            'tracking_number' => 'RESI123456789',
        ]);
    }
}