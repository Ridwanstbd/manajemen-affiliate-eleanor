<?php

namespace Tests\Unit\Admin;

use App\Models\Challenge;
use App\Models\User;
use App\Services\Admin\ChallengeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChallengeServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChallengeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ChallengeService();
        Storage::fake('public');
    }

    private function challengeData(array $overrides = []): array
    {
        return array_merge([
            'title'            => 'Challenge Test',
            'rules'            => 'Aturan challenge test.',
            'start_date'       => now()->toDateString(),
            'end_date'         => now()->addDays(30)->toDateString(),
            'commission_bonus' => 5.0,
            'is_active'        => true,
        ], $overrides);
    }

    // target_metric & reward_description wajib ada di tabel challenge_rewards
    private function rewardPayload(array $overrides = []): array
    {
        return array_merge([
            'target_metric'      => 'GMV',
            'reward_description' => 'Hadiah Test',
        ], $overrides);
    }

    // ─── create ───────────────────────────────────────────────────────────────

    public function test_create_saves_challenge_to_database()
    {
        $challenge = $this->service->create($this->challengeData());

        $this->assertDatabaseHas('challenges', [
            'title'     => 'Challenge Test',
            'is_active' => true,
        ]);
        $this->assertInstanceOf(Challenge::class, $challenge);
    }

    public function test_create_saves_banner_image_when_provided()
    {
        $file = UploadedFile::fake()->image('banner.jpg');
        $data = $this->challengeData(['banner_image' => $file]);

        $challenge = $this->service->create($data);

        Storage::disk('public')->assertExists($challenge->banner_image_path);
    }

    public function test_create_without_banner_image_sets_null_path()
    {
        $challenge = $this->service->create($this->challengeData());

        $this->assertNull($challenge->banner_image_path);
    }

    public function test_create_with_rewards_saves_rewards()
    {
        $data = $this->challengeData([
            'rewards' => [
                $this->rewardPayload(['rank' => 1, 'reward_description' => 'Hadiah Pertama']),
                $this->rewardPayload(['rank' => 2, 'reward_description' => 'Hadiah Kedua']),
            ]
        ]);

        $challenge = $this->service->create($data);

        $this->assertCount(2, $challenge->rewards);
    }

    public function test_create_without_rewards_saves_no_rewards()
    {
        $challenge = $this->service->create($this->challengeData());

        $this->assertCount(0, $challenge->rewards);
    }

    public function test_create_rolls_back_on_failure()
    {
        $this->expectException(\Exception::class);

        // Paksa gagal dengan data tidak valid
        $this->service->create(['title' => null]);

        $this->assertDatabaseCount('challenges', 0);
    }

    // ─── update ───────────────────────────────────────────────────────────────

    public function test_update_modifies_existing_challenge()
    {
        $challenge = Challenge::create($this->challengeData());

        $this->service->update($challenge->id, $this->challengeData(['title' => 'Judul Baru']));

        $this->assertDatabaseHas('challenges', [
            'id'    => $challenge->id,
            'title' => 'Judul Baru',
        ]);
    }

    public function test_update_replaces_old_banner_with_new_one()
    {
        $oldFile  = UploadedFile::fake()->image('old.jpg');
        $oldPath  = $oldFile->store('challenges', 'public');
        $challenge = Challenge::create($this->challengeData(['banner_image_path' => $oldPath]));

        $newFile = UploadedFile::fake()->image('new.jpg');
        $this->service->update($challenge->id, $this->challengeData(['banner_image' => $newFile]));

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($challenge->fresh()->banner_image_path);
    }

    public function test_update_replaces_rewards_when_provided()
    {
        $data      = $this->challengeData([
            'rewards' => [$this->rewardPayload(['reward_description' => 'Hadiah Lama'])],
        ]);
        $challenge = $this->service->create($data);

        $this->service->update($challenge->id, $this->challengeData([
            'rewards' => [
                $this->rewardPayload(['reward_description' => 'Hadiah Baru A']),
                $this->rewardPayload(['reward_description' => 'Hadiah Baru B']),
            ],
        ]));

        $this->assertCount(2, $challenge->fresh()->rewards);
    }

    public function test_update_throws_exception_for_nonexistent_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->update(99999, $this->challengeData());
    }

    // ─── delete ───────────────────────────────────────────────────────────────

    public function test_delete_removes_challenge_from_database()
    {
        $challenge = Challenge::create($this->challengeData());

        $this->service->delete($challenge->id);

        $this->assertDatabaseMissing('challenges', ['id' => $challenge->id]);
    }

    public function test_delete_removes_banner_image_from_storage()
    {
        $file      = UploadedFile::fake()->image('banner.jpg');
        $path      = $file->store('challenges', 'public');
        $challenge = Challenge::create($this->challengeData(['banner_image_path' => $path]));

        $this->service->delete($challenge->id);

        Storage::disk('public')->assertMissing($path);
    }

    public function test_delete_throws_exception_for_nonexistent_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->delete(99999);
    }

    // ─── assignWinner ─────────────────────────────────────────────────────────

    public function test_assign_winner_creates_record()
    {
        $challenge = Challenge::create($this->challengeData());
        $user      = User::factory()->create(['role' => 'AFFILIATOR']);

        $this->service->assignWinner($challenge->id, [
            'user_id'      => $user->id,
            'category'     => 'GMV Tertinggi',
            'reward_given' => 'Voucher Rp 500.000',
        ]);

        $this->assertDatabaseHas('challenge_winners', [
            'challenge_id' => $challenge->id,
            'user_id'      => $user->id,
            'category'     => 'GMV Tertinggi',
        ]);
    }

    // ─── suggestTopGMV & suggestTopVideoCount ─────────────────────────────────

    public function test_suggest_top_gmv_returns_collection()
    {
        $challenge = Challenge::create($this->challengeData());

        $result = $this->service->suggestTopGMV($challenge, 5);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_suggest_top_video_count_returns_collection()
    {
        $challenge = Challenge::create($this->challengeData());

        $result = $this->service->suggestTopVideoCount($challenge, 5);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
}