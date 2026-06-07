<?php

namespace Tests\Unit\Admin;

use App\Models\Product;
use App\Models\SampleRequest;
use App\Models\SampleRequestDetail;
use App\Models\Setting;
use App\Models\TaskReport;
use App\Models\User;
use App\Services\Admin\RequestSampleService;
use App\Services\Admin\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    private SettingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SettingService();
    }

    public function test_update_task_deadline_days_creates_setting_when_not_exists()
    {
        $this->service->updateTaskDeadlineDays(14);

        $this->assertDatabaseHas('settings', [
            'key'   => 'task_deadline_days',
            'value' => '14',
        ]);
    }

    public function test_update_task_deadline_days_updates_existing_setting()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '7']);

        $this->service->updateTaskDeadlineDays(21);

        $this->assertDatabaseHas('settings', [
            'key'   => 'task_deadline_days',
            'value' => '21',
        ]);
        $this->assertDatabaseCount('settings', 1);
    }

    public function test_update_task_deadline_days_returns_setting_model()
    {
        $result = $this->service->updateTaskDeadlineDays(10);

        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('task_deadline_days', $result->key);
        $this->assertEquals('10', $result->value);
    }

    public function test_update_task_deadline_days_with_zero_value()
    {
        $result = $this->service->updateTaskDeadlineDays(0);

        $this->assertEquals('0', $result->value);
    }
}


// ═══════════════════════════════════════════════════════════════════════════════
// RequestSampleServiceTest
// ═══════════════════════════════════════════════════════════════════════════════

class RequestSampleServiceTest extends TestCase
{
    use RefreshDatabase;

    private RequestSampleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RequestSampleService();
    }

    private function makeUser(): User
    {
        return User::factory()->create(['role' => 'AFFILIATOR']);
    }

    private function makeSampleRequest(string $status = 'PENDING'): SampleRequest
    {
        return SampleRequest::factory()->create([
            'user_id' => $this->makeUser()->id,
            'status'  => $status,
        ]);
    }

    private function makeDetail(SampleRequest $sr, string $status = 'APPROVED'): SampleRequestDetail
    {
        $product = Product::create([
            'id'         => (string) rand(1000000, 9999999),
            'name'       => 'Produk Test',
            'category'   => 'Umum',
            'seller_sku' => 'SKU-' . uniqid(),
            'price'      => 50000,
            'is_visible' => true,
        ]);

        return SampleRequestDetail::create([
            'sample_request_id'    => $sr->id,
            'product_id'           => $product->id,
            'quantity'             => 1,
            'status'               => $status,
            'mandatory_video_count'=> 0,
        ]);
    }

    // ─── approve ──────────────────────────────────────────────────────────────

    public function test_approve_changes_status_to_approved()
    {
        $sr     = $this->makeSampleRequest('PENDING');
        $detail = $this->makeDetail($sr, 'APPROVED');

        $result = $this->service->approve($sr->id);

        $this->assertEquals('APPROVED', $result->fresh()->status);
    }

    public function test_approve_throws_when_detail_still_pending()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gagal menyetujui');

        $sr     = $this->makeSampleRequest('PENDING');
        $detail = $this->makeDetail($sr, 'PENDING');

        $this->service->approve($sr->id);
    }

    public function test_approve_throws_when_all_details_rejected()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Semua produk ditolak');

        $sr     = $this->makeSampleRequest('PENDING');
        $detail = $this->makeDetail($sr, 'REJECTED');

        $this->service->approve($sr->id);
    }

    public function test_approve_throws_for_nonexistent_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->approve(99999);
    }

    // ─── ship ─────────────────────────────────────────────────────────────────

    public function test_ship_changes_status_to_shipped()
    {
        $sr = $this->makeSampleRequest('APPROVED');

        $this->service->ship($sr->id, [
            'courier'        => 'JNE',
            'tracking_number'=> 'RESI123456',
        ]);

        $this->assertDatabaseHas('sample_requests', [
            'id'             => $sr->id,
            'status'         => 'SHIPPED',
            'courier'        => 'JNE',
            'tracking_number'=> 'RESI123456',
        ]);
    }

    public function test_ship_sets_shipping_cost_to_zero_when_not_provided()
    {
        $sr = $this->makeSampleRequest('APPROVED');

        $this->service->ship($sr->id, [
            'courier'        => 'SiCepat',
            'tracking_number'=> 'SC999',
        ]);

        $this->assertDatabaseHas('sample_requests', [
            'id'           => $sr->id,
            'shipping_cost'=> 0,
        ]);
    }

    public function test_ship_throws_for_nonexistent_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->ship(99999, ['courier' => 'JNE', 'tracking_number' => 'X']);
    }

    // ─── approveProduct ───────────────────────────────────────────────────────

    public function test_approve_product_changes_detail_status()
    {
        $sr     = $this->makeSampleRequest();
        $detail = $this->makeDetail($sr, 'PENDING');

        $this->service->approveProduct($detail->id, 2);

        $this->assertDatabaseHas('sample_request_details', [
            'id'                   => $detail->id,
            'status'               => 'APPROVED',
            'mandatory_video_count'=> 2,
        ]);
    }

    // ─── rejectProduct ────────────────────────────────────────────────────────

    public function test_reject_product_changes_detail_status_and_saves_reason()
    {
        $sr     = $this->makeSampleRequest();
        $detail = $this->makeDetail($sr, 'PENDING');

        $this->service->rejectProduct($detail->id, 'Stok habis');

        $this->assertDatabaseHas('sample_request_details', [
            'id'           => $detail->id,
            'status'       => 'REJECTED',
            'reject_reason'=> 'Stok habis',
        ]);
    }

    // ─── rejectRequest ────────────────────────────────────────────────────────

    public function test_reject_request_changes_status_and_saves_reason()
    {
        $sr = $this->makeSampleRequest('PENDING');

        $result = $this->service->rejectRequest($sr->id, 'Alamat tidak valid');

        $this->assertEquals('REJECTED', $result->status);
        $this->assertEquals('Alamat tidak valid', $result->reject_reason);
    }

    public function test_reject_request_throws_for_nonexistent_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->rejectRequest(99999, 'Alasan');
    }

    // ─── generateTaskForSample ────────────────────────────────────────────────

    public function test_generate_task_creates_task_reports_for_approved_details()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '7']);

        $user = $this->makeUser();
        $sr   = SampleRequest::factory()->create(['user_id' => $user->id, 'status' => 'DELIVERED']);
        $detail = $this->makeDetail($sr, 'APPROVED');

        SampleRequestDetail::find($detail->id)->update(['mandatory_video_count' => 2]);

        $this->service->generateTaskForDelivered($sr);

        $this->assertEquals(
            2,
            TaskReport::where('user_id', $user->id)->count()
        );
    }

    public function test_generate_task_does_not_duplicate_when_called_twice()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '7']);

        $user = $this->makeUser();
        $sr   = SampleRequest::factory()->create(['user_id' => $user->id, 'status' => 'DELIVERED']);
        $this->makeDetail($sr, 'APPROVED');

        $this->service->generateTaskForDelivered($sr);
        $this->service->generateTaskForDelivered($sr);

        $this->assertEquals(1, TaskReport::where('user_id', $user->id)->count());
    }

    public function test_generate_task_skips_rejected_details()
    {
        $user = $this->makeUser();
        $sr   = SampleRequest::factory()->create(['user_id' => $user->id, 'status' => 'DELIVERED']);
        $this->makeDetail($sr, 'REJECTED');

        $this->service->generateTaskForDelivered($sr);

        $this->assertEquals(0, TaskReport::where('user_id', $user->id)->count());
    }
}