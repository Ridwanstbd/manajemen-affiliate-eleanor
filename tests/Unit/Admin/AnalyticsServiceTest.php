<?php

namespace Tests\Unit\Admin;

use App\Services\Admin\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private AnalyticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AnalyticsService();
    }

    // ─── calcTrend (via reflection) ──────────────────────────────────────────

    private function calcTrend(float $current, float $previous): float
    {
        $ref = new \ReflectionMethod(AnalyticsService::class, 'calcTrend');
        $ref->setAccessible(true);
        return $ref->invoke($this->service, $current, $previous);
    }

    public function test_calc_trend_returns_100_when_previous_is_zero_and_current_positive()
    {
        $this->assertEquals(100, $this->calcTrend(500, 0));
    }

    public function test_calc_trend_returns_0_when_both_zero()
    {
        $this->assertEquals(0, $this->calcTrend(0, 0));
    }

    public function test_calc_trend_returns_correct_positive_percentage()
    {
        // (200 - 100) / 100 * 100 = 100%
        $this->assertEquals(100.0, $this->calcTrend(200, 100));
    }

    public function test_calc_trend_returns_correct_negative_percentage()
    {
        // (50 - 100) / 100 * 100 = -50%
        $this->assertEquals(-50.0, $this->calcTrend(50, 100));
    }

    public function test_calc_trend_handles_negative_previous()
    {
        // (0 - (-50)) / 50 * 100 = 100%
        $this->assertEquals(100.0, $this->calcTrend(0, -50));
    }

    // ─── getTabData routing ───────────────────────────────────────────────────

    public function test_get_tab_data_returns_array_for_analytics_tab()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('analytics', $request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('metrics', $result);
        $this->assertArrayHasKey('funnel', $result);
        $this->assertArrayHasKey('topProducts', $result);
    }

    public function test_get_tab_data_returns_array_for_summary_tab()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('summary', $request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('metrics', $result);
        $this->assertArrayHasKey('top5', $result);
        $this->assertArrayHasKey('statusTugas', $result);
        $this->assertArrayHasKey('trenHarian', $result);
    }

    public function test_get_tab_data_returns_array_for_detail_tab()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('detail', $request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sumberKonversi', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('totalOrders', $result);
    }

    public function test_get_tab_data_defaults_to_analytics_for_unknown_tab()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('tidak-dikenal', $request);

        $this->assertArrayHasKey('metrics', $result);
        $this->assertArrayHasKey('funnel', $result);
    }

    // ─── metrics structure ────────────────────────────────────────────────────

    public function test_analytics_metrics_has_biaya_gmv_roi_keys()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('analytics', $request);

        $this->assertArrayHasKey('biaya', $result['metrics']);
        $this->assertArrayHasKey('gmv', $result['metrics']);
        $this->assertArrayHasKey('roi', $result['metrics']);
    }

    public function test_summary_metrics_has_gmv_items_komisi_sampel_keys()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('summary', $request);

        $this->assertArrayHasKey('gmv', $result['metrics']);
        $this->assertArrayHasKey('items', $result['metrics']);
        $this->assertArrayHasKey('komisi', $result['metrics']);
        $this->assertArrayHasKey('sampel', $result['metrics']);
    }

    // ─── getTargetDate fallback ───────────────────────────────────────────────

    public function test_target_date_falls_back_to_now_when_no_import_history()
    {
        $request = Request::create('/', 'GET');
        // Tidak ada ImportHistory di DB, harus tidak throw
        $result = $this->service->getTabData('analytics', $request);
        $this->assertIsArray($result);
    }

    public function test_target_date_uses_selected_month_from_request()
    {
        $request = Request::create('/', 'GET', ['selected_month' => '2025-01']);
        $result  = $this->service->getTabData('analytics', $request);
        $this->assertIsArray($result);
    }

    // ─── tren harian kosong ───────────────────────────────────────────────────

    public function test_tren_harian_returns_placeholder_when_no_import_history()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('summary', $request);

        $this->assertEquals(['Belum ada data'], $result['trenHarian']['labels']);
        $this->assertEquals([0], $result['trenHarian']['gmv']);
    }

    // ─── funnel structure ─────────────────────────────────────────────────────

    public function test_funnel_contains_required_keys()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('analytics', $request);
        $funnel  = $result['funnel'];

        $this->assertArrayHasKey('total', $funnel);
        $this->assertArrayHasKey('approved', $funnel);
        $this->assertArrayHasKey('approved_pct', $funnel);
        $this->assertArrayHasKey('content', $funnel);
        $this->assertArrayHasKey('content_pct', $funnel);
        $this->assertArrayHasKey('conversion', $funnel);
    }

    public function test_funnel_approved_pct_is_zero_when_no_requests()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('analytics', $request);

        $this->assertEquals(0, $result['funnel']['approved_pct']);
    }

    // ─── komparasi data ───────────────────────────────────────────────────────

    public function test_komparasi_data_contains_two_categories()
    {
        $request = Request::create('/', 'GET');
        $result  = $this->service->getTabData('analytics', $request);

        $this->assertCount(2, $result['komparasiData']);
    }
}