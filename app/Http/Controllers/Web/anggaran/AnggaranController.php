<?php

namespace App\Http\Controllers\Web\anggaran;

use Illuminate\Http\Request;
use App\Models\BbmAnggaran;
use App\Models\BbmAnggaranUpt;
use App\Models\BbmTagihan;
use App\Models\MUpt;
use App\Models\ConfUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class AnggaranController extends Controller
{

    public function entriAnggaran()
    {
        return view('anggaran.entri-anggaran');
    }

    public function getEntriAnggaranData(Request $request)
    {
        $query = BbmAnggaran::select(
            'periode',
            'perubahan_ke',
            'keterangan',
            'user_input',
            'tanggal_input',
            'statusanggaran',
            'user_app',
            'tanggal_app',
            DB::raw('SUM(anggaran) as total_anggaran')
        )
            ->with(['userInput'])
            ->where('perubahan_ke', 0);

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('periode', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%")
                    ->orWhere('user_input', 'LIKE', "%{$search}%");
            });
        }

        // // Apply status filter
        if ($request->status) {
            $query->where('statusanggaran', $request->status);
        }

        // Apply date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('tanggal_input', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('tanggal_input', '<=', $request->date_to);
        }

        $data = $query->groupBy('periode', 'perubahan_ke', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran', 'user_app', 'tanggal_app')
            ->orderBy('periode', 'desc')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function createEntriAnggaran(Request $request)
    {
        $periode = $request->input('periode');
        $keterangan = $request->input('keterangan');
        $anggaranData = $request->input('anggaran_data'); // Array dengan m_upt_code dan anggaran

        DB::beginTransaction();
        try {
            // Get max anggaran_id and add 1
            $maxAnggaranId = BbmAnggaran::max('anggaran_id') ?? 0;
            $newAnggaranId = $maxAnggaranId + 1;

            foreach ($anggaranData as $data) {
                BbmAnggaran::create([
                    'anggaran_id' => $newAnggaranId,
                    'periode' => $periode,
                    'm_upt_code' => $data['m_upt_code'],
                    'anggaran' => $data['anggaran'],
                    'perubahan_ke' => 0,
                    'keterangan' => $keterangan,
                    'statusanggaran' => 0,
                    'user_input' => auth()->user()->username ?? 'admin',
                    'tanggal_input' => now()
                ]);
                $newAnggaranId++; // Increment for next record
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data anggaran berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function viewEntriAnggaran($periode, $perubahanKe)
    {
        $data = BbmAnggaran::with(['upt'])
            ->where('periode', $periode)
            ->where('perubahan_ke', $perubahanKe)
            ->get();

        return response()->json(['data' => $data]);
    }

    public function editEntriAnggaran($periode, $perubahanKe)
    {
        $data = BbmAnggaran::with(['upt'])
            ->where('periode', $periode)
            ->where('perubahan_ke', $perubahanKe)
            ->get();

        return response()->json(['data' => $data]);
    }

    public function updateEntriAnggaran(Request $request)
    {
        $periode = $request->input('periode');
        $perubahanKe = $request->input('perubahan_ke');
        $keterangan = $request->input('keterangan');
        $anggaranData = $request->input('anggaran_data');

        DB::beginTransaction();
        try {
            // Hapus data lama
            BbmAnggaran::where('periode', $periode)
                ->where('perubahan_ke', $perubahanKe)
                ->delete();

            // Get max anggaran_id and add 1
            $maxAnggaranId = BbmAnggaran::max('anggaran_id') ?? 0;
            $newAnggaranId = $maxAnggaranId + 1;

            // Insert data baru
            foreach ($anggaranData as $data) {
                BbmAnggaran::create([
                    'anggaran_id' => $newAnggaranId,
                    'periode' => $periode,
                    'm_upt_code' => $data['m_upt_code'],
                    'anggaran' => $data['anggaran'],
                    'perubahan_ke' => $perubahanKe,
                    'keterangan' => $keterangan,
                    'statusanggaran' => 0,
                    'user_input' => auth()->user()->username ?? 'admin',
                    'tanggal_input' => now()
                ]);
                $newAnggaranId++; // Increment for next record
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data anggaran berhasil diupdate']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate data: ' . $e->getMessage()]);
        }
    }

    public function deleteEntriAnggaran($periode, $perubahanKe)
    {
        try {
            BbmAnggaran::where('periode', $periode)
                ->where('perubahan_ke', $perubahanKe)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Data anggaran berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    // ==================== PERUBAHAN ANGGARAN ====================

    public function perubahanAnggaran()
    {
        return view('anggaran.perubahan-anggaran');
    }

    public function getPerubahanAnggaranData(Request $request)
    {
        $query = BbmAnggaran::select(
            'periode',
            'perubahan_ke',
            'keterangan',
            'user_input',
            'tanggal_input',
            'statusanggaran',
            'user_app',
            'tanggal_app',
            DB::raw('SUM(anggaran) as total_anggaran')
        )
            ->with(['userInput']);

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('periode', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%")
                    ->orWhere('user_input', 'LIKE', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->status) {
            $query->where('statusanggaran', $request->status);
        }

        // Apply date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('tanggal_input', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('tanggal_input', '<=', $request->date_to);
        }

        $data = $query->groupBy('periode', 'perubahan_ke', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran', 'user_app', 'tanggal_app')
            ->orderBy('periode', 'desc')
            ->orderBy('perubahan_ke', 'asc')
            ->get();

        // Tambahkan flag untuk menandai perubahan terakhir yang sudah disetujui
        $data = $data->map(function ($item) {
            // Cek apakah ini perubahan terakhir yang sudah disetujui
            $maxPerubahan = BbmAnggaran::where('periode', $item->periode)
                ->where('statusanggaran', 1)
                ->max('perubahan_ke');

            $item->is_latest_approved = ($item->statusanggaran == 1 && $item->perubahan_ke == $maxPerubahan);
            return $item;
        });

        return response()->json(['data' => $data]);
    }

    public function createPerubahanAnggaran(Request $request)
    {
        $periode = $request->input('periode');
        $keterangan = $request->input('keterangan');
        $anggaranData = $request->input('anggaran_data');

        // Get max perubahan_ke untuk periode ini
        $maxPerubahan = BbmAnggaran::where('periode', $periode)->max('perubahan_ke');
        $newPerubahan = $maxPerubahan + 1;

        DB::beginTransaction();
        try {
            // Get max anggaran_id and add 1
            $maxAnggaranId = BbmAnggaran::max('anggaran_id') ?? 0;
            $newAnggaranId = $maxAnggaranId + 1;

            foreach ($anggaranData as $data) {
                BbmAnggaran::create([
                    'anggaran_id' => $newAnggaranId,
                    'periode' => $periode,
                    'm_upt_code' => $data['m_upt_code'],
                    'anggaran' => $data['anggaran'],
                    'perubahan_ke' => $newPerubahan,
                    'keterangan' => $keterangan,
                    'statusanggaran' => 0,
                    'user_input' => auth()->user()->username ?? 'admin',
                    'tanggal_input' => now()
                ]);
                $newAnggaranId++; // Increment for next record
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Perubahan anggaran berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan: ' . $e->getMessage()]);
        }
    }

    public function viewPerubahanAnggaran($periode, $perubahanKe)
    {
        $data = BbmAnggaran::with(['upt'])
            ->where('periode', $periode)
            ->where('perubahan_ke', $perubahanKe)
            ->get();

        return response()->json(['data' => $data]);
    }

    public function editPerubahanAnggaran($periode, $perubahanKe)
    {
        $data = BbmAnggaran::with(['upt'])
            ->where('periode', $periode)
            ->where('perubahan_ke', $perubahanKe)
            ->get();

        return response()->json(['data' => $data]);
    }

    public function updatePerubahanAnggaran(Request $request)
    {
        $periode = $request->input('periode');
        $perubahanKe = $request->input('perubahan_ke');
        $keterangan = $request->input('keterangan');
        $anggaranData = $request->input('anggaran_data');

        DB::beginTransaction();
        try {
            // Hapus data lama
            BbmAnggaran::where('periode', $periode)
                ->where('perubahan_ke', $perubahanKe)
                ->delete();

            // Get max anggaran_id and add 1
            $maxAnggaranId = BbmAnggaran::max('anggaran_id') ?? 0;
            $newAnggaranId = $maxAnggaranId + 1;

            // Insert data baru
            foreach ($anggaranData as $data) {
                BbmAnggaran::create([
                    'anggaran_id' => $newAnggaranId,
                    'periode' => $periode,
                    'm_upt_code' => $data['m_upt_code'],
                    'anggaran' => $data['anggaran'],
                    'perubahan_ke' => $perubahanKe,
                    'keterangan' => $keterangan,
                    'statusanggaran' => 0,
                    'user_input' => auth()->user()->username ?? 'admin',
                    'tanggal_input' => now()
                ]);
                $newAnggaranId++; // Increment for next record
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Perubahan anggaran berhasil diupdate']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate perubahan: ' . $e->getMessage()]);
        }
    }

    public function uploadPerubahanAnggaran($periode, $perubahanKe)
    {
        try {
            // Cek status anggaran saat ini
            $currentStatus = BbmAnggaran::where('periode', $periode)
                ->where('perubahan_ke', $perubahanKe)
                ->value('statusanggaran');

            if ($currentStatus == 0) {
                // Status "Belum Disetujui" - Ubah menjadi "Sudah Disetujui"
                BbmAnggaran::where('periode', $periode)
                    ->where('perubahan_ke', $perubahanKe)
                    ->update([
                        'statusanggaran' => 1,
                        'user_app' => auth()->user()->username ?? 'admin',
                        'tanggal_app' => now()
                    ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan perubahan anggaran periode ' . $periode . ' perubahan ke-' . $perubahanKe . ' berhasil disetujui'
                ]);
            } else {
                // Status "Sudah Disetujui" - Buat perubahan baru
                $maxPerubahan = BbmAnggaran::where('periode', $periode)->max('perubahan_ke');
                $newPerubahan = $maxPerubahan + 1;

                // Copy data dari perubahan terakhir
                $lastData = BbmAnggaran::where('periode', $periode)
                    ->where('perubahan_ke', $perubahanKe)
                    ->get();

                if ($lastData->count() > 0) {
                    DB::beginTransaction();
                    try {
                        $maxAnggaranId = BbmAnggaran::max('anggaran_id') ?? 0;
                        $newAnggaranId = $maxAnggaranId + 1;

                        foreach ($lastData as $data) {
                            BbmAnggaran::create([
                                'anggaran_id' => $newAnggaranId,
                                'periode' => $data->periode,
                                'm_upt_code' => $data->m_upt_code,
                                'anggaran' => $data->anggaran,
                                'perubahan_ke' => $newPerubahan,
                                'keterangan' => $data->keterangan . ' (Copy dari perubahan ke-' . $perubahanKe . ')',
                                'statusanggaran' => 0, // Status baru = Belum Disetujui
                                'user_input' => auth()->user()->username ?? 'admin',
                                'tanggal_input' => now()
                            ]);
                            $newAnggaranId++;
                        }

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Perubahan anggaran baru periode ' . $periode . ' perubahan ke-' . $newPerubahan . ' berhasil dibuat'
                        ]);
                    } catch (\Exception $e) {
                        DB::rollback();
                        throw $e;
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data perubahan anggaran tidak ditemukan'
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengajukan perubahan anggaran: ' . $e->getMessage()]);
        }
    }

    public function deletePerubahanAnggaran($periode, $perubahanKe)
    {
        try {
            BbmAnggaran::where('periode', $periode)
                ->where('perubahan_ke', $perubahanKe)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Data perubahan anggaran berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    // ==================== APPROVAL ANGGARAN ====================

    public function approvalAnggaran()
    {
        return view('anggaran.approval-anggaran');
    }

    public function getApprovalAnggaranData(Request $request)
    {
        $query = BbmAnggaran::where('statusanggaran', 0)
            ->select('periode', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran', DB::raw('SUM(anggaran) as total_anggaran'))
            ->with(['userInput'])
            ->groupBy('periode', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran')
            ->orderBy('periode', 'desc');

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function approveAnggaran(Request $request)
    {
        $periode = $request->input('periode');

        try {
            BbmAnggaran::where('periode', $periode)
                ->where('statusanggaran', 0)
                ->update([
                    'statusanggaran' => 1,
                    'user_app' => auth()->user()->username ?? 'admin',
                    'tanggal_app' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Anggaran berhasil disetujui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui anggaran: ' . $e->getMessage()]);
        }
    }

    // ==================== ENTRY REALISASI ====================

    public function entryRealisasi()
    {
        return view('anggaran.entry-realisasi');
    }

    public function getEntryRealisasiData(Request $request)
    {
        // Query bbm_tagihan table instead of bbm_anggaran_upt
        $query = DB::table('bbm_tagihan')
            ->join('m_upt', 'm_upt.code', '=', 'bbm_tagihan.m_upt_code')
            ->select(
                'bbm_tagihan.*',
                'm_upt.nama as upt_nama',
                'm_upt.code as m_upt_code'
            )
            ->orderBy('bbm_tagihan.tanggal_invoice', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m_upt.code', 'LIKE', "%{$search}%")
                    ->orWhere('m_upt.nama', 'LIKE', "%{$search}%")
                    ->orWhere('bbm_tagihan.no_tagihan', 'LIKE', "%{$search}%")
                    ->orWhere('bbm_tagihan.penyedia', 'LIKE', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->status) {
            $query->where('bbm_tagihan.statustagihan', $request->status);
        }

        // Apply date filters
        if ($request->date_from) {
            $query->whereDate('bbm_tagihan.tanggal_invoice', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('bbm_tagihan.tanggal_invoice', '<=', $request->date_to);
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function getFormEntryRealisasi($id = 0)
    {
        $data = null;

        if ($id > 0) {
            $data = BbmAnggaranUpt::with(['upt'])->find($id);
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function createEntryRealisasi(Request $request)
    {
        try {
            // Handle file upload with ID + timestamp format
            $filePath = null;
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $fileNames = [];

                // Get next ID for file naming
                $nextId = DB::table('bbm_tagihan')->max('tagihan_id') + 1;
                $timestamp = time();

                foreach ($files as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $nextId . '_' . $timestamp . '.' . $extension;
                    $file->move(public_path('uploads'), $fileName);
                    $fileNames[] = $fileName;
                    $timestamp++; // Increment timestamp for multiple files
                }
                $filePath = implode(',', $fileNames);
            }

            $maxAnggaranId = BbmTagihan::max('tagihan_id') + 1;

            // Create BBM Tagihan entry with correct field mapping
            BbmTagihan::create([
                'tagihan_id' => $maxAnggaranId,
                'm_upt_code' => $request->input('kode_upt'),
                'no_tagihan' => $request->input('no_tagihan'),
                'tanggal_invoice' => $request->input('tgl_invoice'),
                'no_invoice' => $request->input('no_spt'),
                'penyedia' => $request->input('penyedia'),
                'quantity' => str_replace(',', '', $request->input('quantity')),
                'harga' => str_replace(',', '', $request->input('harga')),
                'hargaperliter' => str_replace(',', '', $request->input('hargaperliter')),
                'total' => str_replace(',', '', $request->input('harga')),
                'statustagihan' => 0,
                'tagihanke' => $request->input('tagihanke'),
                'no_spt' => $request->input('no_spt'),
                'file' => $filePath,
                'user_input' => auth()->user()->username ?? 'admin',
                'tanggal_input' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Tagihan BBM berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan tagihan: ' . $e->getMessage()]);
        }
    }

    public function updateEntryRealisasi(Request $request)
    {
        try {
            $id = $request->input('id');

            BbmAnggaranUpt::where('anggaran_upt_id', $id)->update([
                'tanggal_trans' => $request->input('tanggal_trans'),
                'm_upt_code' => $request->input('m_upt_code'),
                'nominal' => str_replace(',', '', $request->input('nominal')),
                'nomor_surat' => $request->input('nomor_surat'),
                'keterangan' => $request->input('keterangan'),
                'user_input' => auth()->user()->username ?? 'admin',
                'tanggal_input' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Realisasi berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate realisasi: ' . $e->getMessage()]);
        }
    }

    public function deleteEntryRealisasi($id)
    {
        try {
            BbmAnggaranUpt::where('anggaran_upt_id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Realisasi berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus realisasi: ' . $e->getMessage()]);
        }
    }

    // ==================== APPROVAL REALISASI ====================

    public function approvalRealisasi()
    {
        return view('anggaran.approval-realisasi');
    }

    public function getApprovalRealisasiData(Request $request)
    {
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->whereIn('statusperubahan', [0, 2])
            ->orderBy('tanggal_trans', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m_upt_code', 'LIKE', "%{$search}%")
                    ->orWhere('user_input', 'LIKE', "%{$search}%")
                    ->orWhere('nomor_surat', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function getViewApprovalRealisasi($id)
    {
        $data = BbmAnggaranUpt::with(['upt', 'userInput'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function approveRealisasi(Request $request)
    {
        $id = $request->input('id');

        try {
            BbmAnggaranUpt::where('anggaran_upt_id', $id)
                ->update([
                    'statusperubahan' => 1,
                    'user_app' => auth()->user()->username ?? 'admin',
                    'tanggal_app' => now()
                ]);

            // Sinkronisasi ke bbm_tagihan untuk dashboard
            $this->syncToBbmTagihan($id);

            return response()->json(['success' => true, 'message' => 'Realisasi berhasil disetujui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui realisasi: ' . $e->getMessage()]);
        }
    }

    private function syncToBbmTagihan($anggaranUptId)
    {
        try {
            $realisasi = BbmAnggaranUpt::find($anggaranUptId);

            if (!$realisasi) {
                return;
            }

            // Cek apakah sudah ada di bbm_tagihan
            $existingTagihan = BbmTagihan::where('m_upt_code', $realisasi->m_upt_code)
                ->where('tanggal_invoice', $realisasi->tanggal_trans)
                ->where('total', $realisasi->nominal)
                ->first();

            if (!$existingTagihan) {
                // Generate nomor tagihan
                $noTagihan = 'TAG-' . date('Ymd') . '-' . str_pad($anggaranUptId, 4, '0', STR_PAD_LEFT);

                // Generate tagihan_id
                $maxId = BbmTagihan::max('tagihan_id') ?? 0;
                $tagihanId = $maxId + 1;

                // Insert ke bbm_tagihan
                BbmTagihan::create([
                    'tagihan_id' => $tagihanId,
                    'm_upt_code' => $realisasi->m_upt_code,
                    'no_tagihan' => $noTagihan,
                    'tanggal_invoice' => $realisasi->tanggal_trans,
                    'no_invoice' => $realisasi->nomor_surat ?? $noTagihan,
                    'penyedia' => 'Sistem Otomatis',
                    'quantity' => 1,
                    'harga' => $realisasi->nominal,
                    'hargaperliter' => $realisasi->nominal,
                    'ppn' => 0,
                    'total' => $realisasi->nominal,
                    'statustagihan' => 1, // Langsung approved
                    'user_input' => $realisasi->user_input,
                    'tanggal_input' => $realisasi->tanggal_input,
                    'user_app' => $realisasi->user_app,
                    'tanggal_app' => $realisasi->tanggal_app
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the approval
            Log::error('Failed to sync to bbm_tagihan: ' . $e->getMessage());
        }
    }

    public function syncAllApprovedRealisasi()
    {
        try {
            $approvedRealisasi = BbmAnggaranUpt::where('statusperubahan', 1)->get();
            $syncedCount = 0;

            foreach ($approvedRealisasi as $realisasi) {
                $this->syncToBbmTagihan($realisasi->anggaran_upt_id);
                $syncedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil sinkronisasi {$syncedCount} data realisasi ke bbm_tagihan"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== PEMBATALAN REALISASI ====================

    public function pembatalanRealisasi()
    {
        return view('anggaran.pembatalan-realisasi');
    }

    public function getPembatalanRealisasiData(Request $request)
    {
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->where('statusperubahan', 1)
            ->orderBy('tanggal_trans', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m_upt_code', 'LIKE', "%{$search}%")
                    ->orWhere('user_input', 'LIKE', "%{$search}%")
                    ->orWhere('nomor_surat', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function getViewPembatalanRealisasi($id)
    {
        $data = BbmAnggaranUpt::with(['upt', 'userInput'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function cancelRealisasi(Request $request)
    {
        $id = $request->input('id');

        try {
            BbmAnggaranUpt::where('anggaran_upt_id', $id)
                ->update([
                    'statusperubahan' => 2,
                    'user_app' => auth()->user()->username ?? 'admin',
                    'tanggal_app' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Realisasi berhasil dibatalkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan realisasi: ' . $e->getMessage()]);
        }
    }

    // ==================== TANGGAL SPPD ====================

    public function tanggalSppd()
    {
        return view('anggaran.tanggal-sppd');
    }

    public function getTanggalSppdData(Request $request)
    {
        // Implementasi untuk data SPPD
        $data = [];
        return response()->json(['data' => $data]);
    }

    // ==================== ENTRY ANGGARAN INTERNAL ====================

    public function entryAnggaranInternal()
    {
        return view('anggaran.entry-anggaran-internal');
    }

    public function getEntryAnggaranInternalData(Request $request)
    {
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->orderBy('tanggal_trans', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m_upt_code', 'LIKE', "%{$search}%")
                    ->orWhere('user_input', 'LIKE', "%{$search}%")
                    ->orWhere('nomor_surat', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function getFormEntryAnggaranInternal($id = 0)
    {
        $data = [];
        $upts = MUpt::all();

        if ($id > 0) {
            $data = BbmAnggaranUpt::with(['upt'])->find($id);
        }

        return response()->json([
            'data' => $data,
            'upts' => $upts
        ]);
    }

    public function createEntryAnggaranInternal(Request $request)
    {
        try {
            BbmAnggaranUpt::create([
                'tanggal_trans' => $request->input('tanggal_trans'),
                'm_upt_code' => $request->input('m_upt_code'),
                'nominal' => str_replace(',', '', $request->input('nominal')),
                'nomor_surat' => $request->input('nomor_surat'),
                'keterangan' => $request->input('keterangan'),
                'statusperubahan' => 0,
                'user_input' => auth()->user()->username ?? 'admin',
                'tanggal_input' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Anggaran internal berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan anggaran internal: ' . $e->getMessage()]);
        }
    }

    public function updateEntryAnggaranInternal(Request $request)
    {
        try {
            $id = $request->input('id');

            BbmAnggaranUpt::where('anggaran_upt_id', $id)->update([
                'tanggal_trans' => $request->input('tanggal_trans'),
                'm_upt_code' => $request->input('m_upt_code'),
                'nominal' => str_replace(',', '', $request->input('nominal')),
                'nomor_surat' => $request->input('nomor_surat'),
                'keterangan' => $request->input('keterangan'),
                'user_input' => auth()->user()->username ?? 'admin',
                'tanggal_input' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Anggaran internal berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate anggaran internal: ' . $e->getMessage()]);
        }
    }

    public function deleteEntryAnggaranInternal($id)
    {
        try {
            BbmAnggaranUpt::where('anggaran_upt_id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Anggaran internal berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus anggaran internal: ' . $e->getMessage()]);
        }
    }

    // ==================== APPROVAL ANGGARAN INTERNAL ====================

    public function approvalAnggaranInternal()
    {
        return view('anggaran.approval-anggaran-internal');
    }

    public function getApprovalAnggaranInternalData(Request $request)
    {
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->whereIn('statusperubahan', [0, 2])
            ->orderBy('tanggal_trans', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m_upt_code', 'LIKE', "%{$search}%")
                    ->orWhere('user_input', 'LIKE', "%{$search}%")
                    ->orWhere('nomor_surat', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function getViewApprovalAnggaranInternal($id)
    {
        $data = BbmAnggaranUpt::with(['upt', 'userInput'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function approveAnggaranInternal(Request $request)
    {
        $id = $request->input('id');

        try {
            BbmAnggaranUpt::where('anggaran_upt_id', $id)
                ->update([
                    'statusperubahan' => 1,
                    'user_app' => auth()->user()->username ?? 'admin',
                    'tanggal_app' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Anggaran internal berhasil disetujui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui anggaran internal: ' . $e->getMessage()]);
        }
    }

    // ==================== PEMBATALAN ANGGARAN INTERNAL ====================

    public function pembatalanAnggaranInternal()
    {
        return view('anggaran.pembatalan-anggaran-internal');
    }

    public function getPembatalanAnggaranInternalData(Request $request)
    {
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->where('statusperubahan', 1)
            ->orderBy('tanggal_trans', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m_upt_code', 'LIKE', "%{$search}%")
                    ->orWhere('user_input', 'LIKE', "%{$search}%")
                    ->orWhere('nomor_surat', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function cancelAnggaranInternal(Request $request)
    {
        $id = $request->input('id');

        try {
            BbmAnggaranUpt::where('anggaran_upt_id', $id)
                ->update([
                    'statusperubahan' => 2,
                    'user_app' => auth()->user()->username ?? 'admin',
                    'tanggal_app' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Anggaran internal berhasil dibatalkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan anggaran internal: ' . $e->getMessage()]);
        }
    }

    // ==================== HELPER METHODS ====================

    public function getUptOptions()
    {
        $upts = MUpt::select('code as id', 'nama as nama_upt')->get();
        return response()->json($upts);
    }

    public function getAnggaranData(Request $request)
    {
        $uptCode = $request->input('kode_upt');
        $tahun = $request->input('tahun', date('Y'));

        $anggaran = BbmAnggaran::where('m_upt_code', $uptCode)
            ->where('periode', $tahun)
            ->orderBy('perubahan_ke', 'desc')
            ->first();

        return response()->json([
            'anggaran' => $anggaran ? $anggaran->anggaran : 0
        ]);
    }

    public function getNominalAwal(Request $request)
    {
        $uptCode = $request->input('kode_upt');
        $tanggalTrans = $request->input('tanggal_trans');
        $tahun = date('Y', strtotime($tanggalTrans));
        $tglAwal = $tahun . '-01-01';

        $nominal = BbmAnggaranUpt::where('m_upt_code', $uptCode)
            ->where('tanggal_trans', '>=', $tglAwal)
            ->where('tanggal_trans', '<', $tanggalTrans)
            ->sum('nominal');

        return response()->json([
            'nominal' => $nominal
        ]);
    }

    public function getDataAnggaran(Request $request)
    {
        $uptCode = $request->input('kode_upt');
        $tahun = $request->input('tahun', date('Y'));

        $anggaran = BbmAnggaran::where('m_upt_code', $uptCode)
            ->where('periode', $tahun)
            ->orderBy('perubahan_ke', 'desc')
            ->first();

        return response()->json([
            'anggaran' => $anggaran ? $anggaran->anggaran : 0
        ]);
    }

    public function getDataAnggaran2(Request $request, $id, $tahun)
    {
        $anggaran = BbmAnggaran::where('m_upt_code', $request->input('kode_upt'))
            ->where('periode', $tahun)
            ->orderBy('perubahan_ke', 'desc')
            ->first();

        return response()->json([
            'anggaran' => $anggaran ? $anggaran->anggaran : 0
        ]);
    }

    /**
     * Get UPT Info for current user (similar to project_ci)
     */
    public function getUptInfo()
    {
        $user = Auth::user();
        $upt = MUpt::where('code', $user->m_upt_code)->first();

        if (!$upt) {
            return response()->json([
                'success' => false,
                'message' => 'UPT tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => $upt->code,
            'nama' => $upt->nama,
            'alamat' => $upt->alamat1 ?: $upt->alamat2
        ]);
    }

    /**
     * Get SO Data by multiple SO numbers (similar to project_ci)
     */
    public function getSoData($multino)
    {
        // If multino is 0 or empty, get all data
        if ($multino == '0' || empty($multino)) {
            $data = DB::table('bbm_transdetail')
                ->where('status_bayar', '0')
                ->get();
        } else {
            // Convert multino back to comma-separated SO numbers
            $no_so = str_replace("x", "', '", $multino);

            // Query bbm_transdetail table with specific SO numbers
            $data = DB::table('bbm_transdetail')
                ->whereIn('no_so', explode("', '", $no_so))
                ->where('status_bayar', '0')
                ->get();
        }

        return view('anggaran.partials.so-data-table', compact('data'));
    }

    /**
     * Generate Nomor Tagihan (similar to project_ci)
     */
    public function generateNomorTagihan()
    {
        // Generate nomor tagihan format: YY.MM.DD.XXXXX
        $year = date('y');
        $month = date('m');
        $day = date('d');

        // Get last number for today
        $lastTagihan = DB::table('bbm_tagihan')
            ->where('no_tagihan', 'like', $year . '.' . $month . '.' . $day . '.%')
            ->orderBy('no_tagihan', 'desc')
            ->first();

        if ($lastTagihan) {
            $lastNumber = (int) substr($lastTagihan->no_tagihan, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $nomorTagihan = $year . '.' . $month . '.' . $day . '.' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'nomor_tagihan' => $nomorTagihan
        ]);
    }
}
