<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ImportFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'ADMINISTRATOR'
        ]);
    }

    private function getValidFakeFiles()
    {
        return [
            'file_core_metrics' => UploadedFile::fake()->create('Transaction_Analysis_Core_Metrics_20260301-20260331.xlsx', 100),
            'file_creator_list' => UploadedFile::fake()->create('Transaction_Analysis_Creator_List_20260301-20260331.xlsx', 100),
            'file_live_list'    => UploadedFile::fake()->create('Transaction_Analysis_Live_List_20260301-20260331.xlsx', 100),
            'file_product_list' => UploadedFile::fake()->create('Transaction_Analysis_Product_List_20260301-20260331.xlsx', 100),
            'file_video_list'   => UploadedFile::fake()->create('Transaction_Analysis_Video_List_20260301-20260331.xlsx', 100),
        ];
    }

    public function test_admin_can_import_valid_files_successfully()
    {
        Excel::fake();

        $files = $this->getValidFakeFiles();

        $response = $this->actingAs($this->adminUser)
                         ->from('/dashboard/import-data')
                         ->post('/dashboard/import-data', $files);
        $response->assertStatus(302)
                 ->assertRedirect('/dashboard/import-data')
                 ->assertSessionHas('success', '5 File Excel berhasil diimport dengan rentang data diekstrak dari nama file.');

        Excel::assertImported('Transaction_Analysis_Core_Metrics_20260301-20260331.xlsx', function (\App\Imports\CoreMetricsImport $import) {
            return true;
        });
        Excel::assertImported('Transaction_Analysis_Creator_List_20260301-20260331.xlsx', function (\App\Imports\CreatorListImport $import) {
            return true;
        });
        Excel::assertImported('Transaction_Analysis_Live_List_20260301-20260331.xlsx', function (\App\Imports\LiveListImport $import) {
            return true;
        });
        Excel::assertImported('Transaction_Analysis_Product_List_20260301-20260331.xlsx', function (\App\Imports\ProductListImport $import) {
            return true;
        });
        Excel::assertImported('Transaction_Analysis_Video_List_20260301-20260331.xlsx', function (\App\Imports\VideoListImport $import) {
            return true;
        });

        $this->assertDatabaseHas('import_histories', [
            'start_date' => '2026-03-01',
            'end_date'   => '2026-03-31',
            'admin_id'   => $this->adminUser->id
        ]);
    }

    public function test_import_fails_when_missing_required_files()
    {
        $files = $this->getValidFakeFiles();
        
        unset($files['file_live_list']);

        $response = $this->actingAs($this->adminUser)
                         ->postJson('/dashboard/import-data', $files);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_live_list']);
    }

    public function test_import_fails_with_invalid_file_extension()
    {
        $files = $this->getValidFakeFiles();
        
        $files['file_product_list'] = UploadedFile::fake()->create('Transaction_Analysis_Product_List_20260301-20260331.csv', 100);

        $response = $this->actingAs($this->adminUser)
                         ->postJson('/dashboard/import-data', $files);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_product_list']);
    }

    public function test_import_fails_when_dates_do_not_match()
    {
        $files = $this->getValidFakeFiles();
        
        $files['file_video_list'] = UploadedFile::fake()->create('Transaction_Analysis_Video_List_20260401-20260430.xlsx', 100);

        $response = $this->actingAs($this->adminUser)
                         ->postJson('/dashboard/import-data', $files);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_mismatch']);
    }

    public function test_import_fails_with_wrong_file_prefix()
    {
        $files = $this->getValidFakeFiles();
        
        $files['file_creator_list'] = UploadedFile::fake()->create('Salah_Nama_List_20260301-20260331.xlsx', 100);

        $response = $this->actingAs($this->adminUser)
                         ->postJson('/dashboard/import-data', $files);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file_creator_list']);
    }

    public function test_non_admin_cannot_access_import_route()
    {
        $affiliator = User::factory()->create([
            'role' => 'AFFILIATOR'
        ]);

        $files = $this->getValidFakeFiles();

        // Eksekusi sebagai Affiliator
        $response = $this->actingAs($affiliator)
                         ->postJson('/dashboard/import-data', $files);

        $response->assertStatus(403);
    }
}