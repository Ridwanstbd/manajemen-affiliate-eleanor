<?php

namespace App\Services\Admin;

use App\Models\Blacklist;
use App\Models\ImportHistory;
use App\Models\KOLContract;
use App\Models\ProductTaskReport;
use App\Models\SampleRequest;
use App\Models\SystemAccessRequest;
use App\Models\TaskReport;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function getTabData($tab, Request $request)
    {
        switch ($tab) {
            case 'active':
                return $this->getActiveData($request);
            case 'blacklist':
                return $this->getBlacklistData($request);
            case 'kol-contract':
                return $this->getKOLContractData($request);
            case 'request-access':
            default:
                return $this->getRequestData($request);
        }
    }
    private function getActiveData(Request $request)
    {
        $latestImport = ImportHistory::latest('start_date')->first();
        
        $selectedMonth = $latestImport ? $latestImport->start_date->format('Y-m') : null;
        $selectedLabel = $latestImport ? $latestImport->start_date->translatedFormat('F Y') : 'Belum Ada Data';

        $users = User::whereDoesntHave('blacklists')
            ->where('is_kol', false)
            ->get()
            ->map(function($user) use ($selectedMonth, $selectedLabel) {
                
                $metricQuery = $user->creatorMetrics();
                if ($selectedMonth) {
                    $metricQuery->whereHas('importHistory', function($q) use ($selectedMonth) {
                        $q->whereRaw('DATE_FORMAT(start_date, "%Y-%m") = ?', [$selectedMonth]);
                    });
                }
                
                $metrics = $metricQuery->selectRaw('
                    SUM(affiliate_gmv) as total_gmv,
                    SUM(items_sold) as total_items,
                    SUM(estimated_commission) as total_commission,
                    SUM(samples_sent) as total_samples_metric,
                    AVG(aov) as avg_aov,
                    SUM(refunds) as total_refunds,
                    SUM(items_returned) as total_returned,
                    SUM(video_count) as total_videos,
                    SUM(live_count) as total_lives
                ')->first();

                $operationalSamples = SampleRequest::where('user_id', $user->id)
                    ->whereIn('status', ['APPROVED', 'SHIPPED'])
                    ->when($selectedMonth, function($query) use ($selectedMonth) {
                        $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$selectedMonth]);
                    })
                    ->with('details')
                    ->get()
                    ->sum(function($req) {
                        return $req->details->sum('quantity');
                    });

                $taskIds = TaskReport::whereHas('sampleRequests', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->where('task_status', 'COMPLETED')
                    ->when($selectedMonth, function($query) use ($selectedMonth) {
                        $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$selectedMonth]);
                    })
                    ->pluck('id');
                
                $completedProductTasks = ProductTaskReport::whereIn('task_report_id', $taskIds)->count();

                $user->metrics = $metrics;
                $user->total_samples_received = $operationalSamples > 0 ? $operationalSamples : ($metrics->total_samples_metric ?? 0);
                $user->completed_product_tasks = $completedProductTasks;
                
                return $user;
            });

        return [
            'users' => $users,
        ];
    }
    private function getBlacklistData(Request $request)
    {
        $availableMonths = Blacklist::selectRaw('DISTINCT DATE_FORMAT(blacklist_date, "%Y-%m") as month_val')
            ->orderBy('month_val', 'desc')
            ->get()
            ->map(function($item) {
                $date = Carbon::parse($item->month_val . '-01');
                return [
                    'value' => $item->month_val,
                    'label' => $date->translatedFormat('F Y') 
                ];
            });

        $selectedMonth = $request->input('selected_month');
        $selectedLabel = 'Pilih Bulan';
        if ($selectedMonth) {
            $selectedLabel = Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y');
        } elseif ($availableMonths->isNotEmpty()) {
            $selectedMonth = $availableMonths->first()['value'];
            $selectedLabel = $availableMonths->first()['label'];
        }

        $users = User::where('account_status', 'BANNED')
            ->whereHas('blacklists', function($q) use ($selectedMonth) {
                if ($selectedMonth) {
                    $q->whereRaw('DATE_FORMAT(blacklist_date, "%Y-%m") = ?', [$selectedMonth]);
                }
            })
            ->with(['blacklists' => function($q) {
                $q->latest();
            }])
            ->get()
            ->map(function($user) use ($selectedLabel) {
                
                $metrics = $user->creatorMetrics()->selectRaw('
                    SUM(affiliate_gmv) as total_gmv,
                    SUM(items_sold) as total_items,
                    SUM(estimated_commission) as total_commission,
                    SUM(samples_sent) as total_samples_metric,
                    AVG(aov) as avg_aov,
                    SUM(refunds) as total_refunds,
                    SUM(items_returned) as total_returned,
                    SUM(video_count) as total_videos,
                    SUM(live_count) as total_lives
                ')->first();

                $user->metrics = $metrics;
                $user->blacklist_info = $user->blacklists->first(); 
                $user->month_label = $selectedLabel; 
                
                return $user;
            });

        return [
            'users' => $users,
            'availableMonths' => $availableMonths,
            'selectedMonthLabel' => $selectedLabel,
        ];
    }
    public function addToBlacklist(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);
            $user->update(['account_status' => 'BANNED']);

            return Blacklist::create([
                'user_id' => $user->id,
                'blacklist_date' => now(),
                'violation_reason' => $data['violation_reason'],
            ]);
        });
    }

    public function restoreBlacklist(string $id)
    {
        $user = User::findOrFail($id);
        $user->account_status = 'ACTIVE';
        $user->save();
        $user->blacklists()->delete();

        return $user;
    }
    private function getRequestData(Request $request)
    {
        $availableMonths = SystemAccessRequest::selectRaw('DISTINCT DATE_FORMAT(created_at, "%Y-%m") as month_val')
            ->orderBy('month_val', 'desc')
            ->get()
            ->map(function($item) {
                $date = Carbon::parse($item->month_val . '-01');
                return [
                    'value' => $item->month_val,
                    'label' => $date->translatedFormat('F Y') 
                ];
            });
            
        $selectedMonth = $request->input('selected_month');
        $selectedLabel = 'Pilih Bulan';
        if ($selectedMonth) {
            $selectedLabel = Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y');
        } elseif ($availableMonths->isNotEmpty()) {
            $selectedLabel = $availableMonths->first()['label'];
        }

        $query = SystemAccessRequest::where('status', 'PENDING');

        if ($selectedMonth) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$selectedMonth]);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return [
            'users' => $requests,
            'availableMonths' => $availableMonths,
            'selectedLabel' => $selectedLabel,
        ];
    }
    private function getKOLContractData(Request $request)
    {
        $availableMonths = KOLContract::selectRaw('DISTINCT DATE_FORMAT(created_at, "%Y-%m") as month_val')
            ->orderBy('month_val', 'desc')
            ->get()
            ->map(function($item) {
                $date = Carbon::parse($item->month_val . '-01');
                return [
                    'value' => $item->month_val,
                    'label' => $date->translatedFormat('F Y') 
                ];
            });

        $selectedMonth = $request->input('selected_month');
        $selectedLabel = 'Pilih Bulan';
        if ($selectedMonth) {
            $selectedLabel = Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y');
        } elseif ($availableMonths->isNotEmpty()) {
            $selectedMonth = $availableMonths->first()['value'];
            $selectedLabel = $availableMonths->first()['label'];
        }

        $users = User::where('is_kol', true)
            ->whereHas('kolContracts', function($q) use ($selectedMonth) {
                if ($selectedMonth) {
                    $q->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$selectedMonth]);
                }
            })
            ->with(['kolContracts' => function($q) {
                $q->latest()->with('products'); 
            }])
            ->get()
            ->map(function($user) use ($selectedLabel) {
                $currentContract = $user->kolContracts->first();
                
                $metrics = $user->creatorMetrics()
                    ->whereBetween('created_at', [$currentContract->start_date, $currentContract->end_date])
                    ->selectRaw('SUM(affiliate_gmv) as total_gmv, SUM(video_count) as total_videos')
                    ->first();

                $user->metrics = $metrics;
                $user->active_contract = $currentContract;
                $user->month_label = $selectedLabel;
                
                return $user;
            });

        return [
            'users' => $users,
            'availableMonths' => $availableMonths,
            'selectedMonthLabel' => $selectedLabel,
        ];
    }

    public function extendKOLContract(array $data)
    {
        return DB::transaction(function () use ($data) {
            $oldContract = KOLContract::findOrFail($data['original_contract_id']);
            
            $newContract = KOLContract::create([
                'user_id' => $oldContract->user_id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'contract_fee' => $data['contract_fee'],
                'required_video_count' => $data['required_video_count'],
                'status' => 'ACTIVE',
                'notes' => $data['notes'] ?? 'Perpanjangan kontrak',
            ]);

            $productIds = $oldContract->products()->pluck('products.id')->toArray();
            if (!empty($productIds)) {
                $newContract->products()->attach($productIds);
            }

            $oldContract->update(['status' => 'EXPIRED']);

            return $newContract;
        });
    }

    public function approveAccess(Request $request)
    {
        $accessRequest = SystemAccessRequest::findOrFail($request->id);

        $user = User::create([
            'username' => $accessRequest->tiktok_username,
            'is_kol' => false,
        ]);

        $accessRequest->update([
            'status' => 'APPROVED'
        ]);

        return $user;
    }

    public function rejectAccess(string $id)
    {
        try {
            $userRequest = SystemAccessRequest::findOrFail($id);
            $userRequest->status = 'REJECTED'; 
            $userRequest->save();
            return $userRequest;

        } catch (ModelNotFoundException $e) {
            throw new Exception("Data permintaan akses tidak ditemukan.");
        } catch (Exception $e) {
            throw new Exception("Terjadi kesalahan saat memproses data: " . $e->getMessage());
        }
    }

    public function createKOLContract(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);
            $user->update(['is_kol' => true]);

            $contract = KOLContract::create([
                'user_id' => $user->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'contract_fee' => $data['contract_fee'],
                'required_video_count' => $data['required_video_count'],
                'status' => 'ACTIVE',
                'notes' => $data['notes'] ?? 'Kontrak awal KOL',
            ]);

            if (!empty($data['product_ids'])) {
                $contract->products()->attach($data['product_ids']);
            }

            return $contract;
        });
    }
}