<?php

namespace App\Services\Admin;

use App\Models\Blacklist;
use App\Models\ImportHistory;
use App\Models\Product;
use App\Models\KOLContract;
use App\Models\ProductTaskReport;
use App\Models\SampleRequest;
use App\Models\SystemAccessRequest;
use App\Models\TaskReport;
use App\Models\User;
use App\Models\Agreement;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class UserService
{
    private function extractTextFromFile(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if ($extension === 'pdf') {
            return $this->extractTextFromPdf($file);
        }
        
        return $this->extractTextFromDocx($file);
    }

    private function extractTextFromPdf(UploadedFile $file): string
    {
        $content = file_get_contents($file->getRealPath());
        $text = '';

        if (preg_match_all('/stream(.*?)endstream/is', $content, $matches)) {
            foreach ($matches[1] as $match) {
                $stream = trim($match);
                $uncompressed = @gzuncompress($stream);
                
                if ($uncompressed !== false) {
                    $blocks = preg_split('/(ET|T\*|Td|TD|Tm)/', $uncompressed);
                    
                    foreach ($blocks as $block) {
                        $blockText = '';
                        if (preg_match_all('/\[(.*?)\]\s*TJ/is', $block, $tjMatches)) {
                            foreach ($tjMatches[1] as $tjMatch) {
                                if (preg_match_all('/\((.*?)\)/', $tjMatch, $strMatches)) {
                                    $blockText .= implode('', $strMatches[1]);
                                }
                            }
                        } elseif (preg_match_all('/\((.*?)\)\s*Tj/is', $block, $tjMatches)) {
                            $blockText .= implode(' ', $tjMatches[1]);
                        }
                        
                        $blockText = preg_replace('/\\\\([nrt()\\\\])/', '$1', $blockText);
                        
                        if (trim($blockText) !== '') {
                            $text .= trim($blockText) . "\n";
                        }
                    }
                }
            }
        }

        $text = preg_replace('/\\\\([nrt()\\\\])/', '$1', $text);
        $text = preg_replace('/\\\\[0-9]{3}/', '', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        return trim($text);
    }

    private function extractTextFromDocx(UploadedFile $file): string
    {
        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new Exception('Gagal membuka file .docx. Pastikan file tidak rusak.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new Exception('Isi dokumen tidak ditemukan di dalam file .docx.');
        }

        $xml = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xml);
        $xml = str_replace(['w:', 'r:', 'mc:', 'wp:', 'a:', 'v:', 'o:'], '', $xml);

        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);
        libxml_clear_errors();

        if ($doc === false) {
            return $this->extractTextFromDocxFallback($xml);
        }

        $lines   = [];
        $body    = $doc->body ?? $doc;

        foreach ($body->children() as $block) {
            $lines[] = $this->processBlock($block);
        }

        $text = implode("\n", $lines);

        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    private function processBlock(\SimpleXMLElement $block): string
    {
        $tag = $block->getName();

        if ($tag === 'tbl') {
            $rows = [];
            foreach ($block->children() as $tr) {
                if ($tr->getName() !== 'tr') continue;
                $cells = [];
                foreach ($tr->children() as $tc) {
                    if ($tc->getName() !== 'tc') continue;
                    $cellText = [];
                    foreach ($tc->children() as $cellBlock) {
                        $cellText[] = $this->processBlock($cellBlock);
                    }
                    $cells[] = implode("\n", $cellText);
                }
                $rows[] = implode("\t", $cells);
            }
            return implode("\n", $rows);
        }

        if ($tag === 'p') {
            return $this->processParagraph($block);
        }

        return '';
    }

    private function processParagraph(\SimpleXMLElement $para): string
    {
        $result = '';

        foreach ($para->children() as $child) {
            $tag = $child->getName();

            if ($tag === 'r') {
                foreach ($child->children() as $rChild) {
                    $rTag = $rChild->getName();
                    if ($rTag === 't') {
                        $result .= (string) $rChild;
                    } elseif ($rTag === 'br') {
                        $result .= "\n";
                    } elseif ($rTag === 'tab') {
                        $result .= "\t";
                    }
                }
            }

            if ($tag === 'hyperlink') {
                foreach ($child->children() as $hlChild) {
                    if ($hlChild->getName() === 'r') {
                        foreach ($hlChild->children() as $rChild) {
                            if ($rChild->getName() === 't') {
                                $result .= (string) $rChild;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function extractTextFromDocxFallback(string $strippedXml): string
    {
        $strippedXml = preg_replace('/<\/w:p>/', "\n", $strippedXml);
        $strippedXml = preg_replace('/<w:br[^\/]*\/>/', "\n", $strippedXml);
        $strippedXml = preg_replace('/<w:tab[^\/]*\/>/', "\t", $strippedXml);

        $text = strip_tags($strippedXml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    public function getTabData($tab, Request $request)
    {
        $data = [];

        switch ($tab) {
            case 'active':
                $data = $this->getActiveData($request);
                break;
            case 'blacklist':
                $data = $this->getBlacklistData($request);
                break;
            case 'kol-contract':
                $data = $this->getKOLContractData($request);
                break;
            case 'request-access':
            default:
                $data = $this->getRequestData($request);
                break;
        }

        $data['products'] = Product::select('id', 'name')->get();

        return $data;
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

    public function approveAccess(Request $request, bool $isKol)
    {
        $accessRequest = SystemAccessRequest::findOrFail($request->id);

        $user = User::create([
            'username' => $accessRequest->tiktok_username,
            'is_kol' => $isKol
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

            $agreementContent = $this->extractTextFromFile($data['agreement_file']);

            $agreement = Agreement::create([
                'user_id'   => $user->id,
                'content'   => $agreementContent,
                'is_active' => true,
                'is_kol'    => true,
            ]);

            $contract = KOLContract::create([
                'user_id' => $user->id,
                'agreement_id' => $agreement->id,
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

    public function updateKOLContract(array $data)
    {
        return DB::transaction(function () use ($data) {
            $contract = KOLContract::with('agreement')->findOrFail($data['id']);

            $contract->update([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'contract_fee' => $data['contract_fee'],
                'required_video_count' => $data['required_video_count'],
                'status' => $data['status'] ?? $contract->status,
                'notes' => $data['notes'] ?? $contract->notes,
            ]);

            if (isset($data['product_ids'])) {
                $contract->products()->sync($data['product_ids']);
            }

            if ($contract->agreement) {
                if (!empty($data['agreement_file'])) {
                    $newContent = $this->extractTextFromFile($data['agreement_file']);
                    $contract->agreement->update(['content' => $newContent]);
                }
            } else {
                $content = !empty($data['agreement_file'])
                    ? $this->extractTextFromFile($data['agreement_file'])
                    : ($data['agreement_content'] ?? '');
                $agreement = Agreement::create([
                    'user_id'   => $contract->user_id,
                    'content'   => $content,
                    'is_active' => true,
                    'is_kol'    => true,
                ]);
                $contract->update(['agreement_id' => $agreement->id]);
            }

            return $contract;
        });
    }

    public function destroyKOLContract(int $id)
    {
        return DB::transaction(function () use ($id) {
            $contract = KOLContract::findOrFail($id);
            $user = User::findOrFail($contract->user_id);
            $contractCount = KOLContract::where('user_id', $user->id)->count();
            if ($contractCount <= 1) { 
                $user->update(['is_kol' => false]);
            }

            $contract->products()->detach();

            return $contract->delete();
        });
    }
}