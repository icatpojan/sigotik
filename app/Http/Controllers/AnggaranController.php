<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BbmAnggaran;
use App\Models\BbmAnggaranUpt;
use App\Models\MUpt;
use App\Models\ConfUser;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnggaranController extends Controller
{

    public function entriAnggaran()
    {
        return view('anggaran.entri-anggaran');
    }

    public function getEntriAnggaranData(Request $request)
    {
        // Get data grouped by periode and perubahan_ke
        $data = BbmAnggaran::select('periode', 'perubahan_ke', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran', DB::raw('SUM(anggaran) as total_anggaran'))
            ->where('perubahan_ke', 0)
            ->groupBy('periode', 'perubahan_ke', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran')
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
        $query = BbmAnggaran::with(['upt', 'userInput'])
            ->select('periode', 'perubahan_ke', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran', DB::raw('SUM(anggaran) as total_anggaran'))
            ->groupBy('periode', 'perubahan_ke', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran')
            ->orderBy('periode', 'desc')
            ->orderBy('perubahan_ke', 'asc');

        $data = $query->get();

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

    // ==================== APPROVAL ANGGARAN ====================

    public function approvalAnggaran()
    {
        return view('anggaran.approval-anggaran');
    }

    public function getApprovalAnggaranData(Request $request)
    {
        $query = BbmAnggaran::with(['upt', 'userInput'])
            ->where('statusanggaran', 0)
            ->select('periode', 'keterangan', 'user_input', 'tanggal_input', 'statusanggaran', DB::raw('SUM(anggaran) as total_anggaran'))
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
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->orderBy('tanggal_trans', 'desc');

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function createEntryRealisasi(Request $request)
    {
        try {
            BbmAnggaranUpt::create([
                'tanggal_trans' => $request->input('tanggal_trans'),
                'm_upt_code' => $request->input('m_upt_code'),
                'nominal' => $request->input('nominal'),
                'nomor_surat' => $request->input('nomor_surat'),
                'keterangan' => $request->input('keterangan'),
                'statusperubahan' => 0,
                'user_input' => auth()->user()->username ?? 'admin',
                'tanggal_input' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Realisasi berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan realisasi: ' . $e->getMessage()]);
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

        $data = $query->get();

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

            return response()->json(['success' => true, 'message' => 'Realisasi berhasil disetujui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui realisasi: ' . $e->getMessage()]);
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

        $data = $query->get();

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

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function createEntryAnggaranInternal(Request $request)
    {
        try {
            BbmAnggaranUpt::create([
                'tanggal_trans' => $request->input('tanggal_trans'),
                'm_upt_code' => $request->input('m_upt_code'),
                'nominal' => $request->input('nominal'),
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

        $data = $query->get();

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
            'anggaran' => $anggaran ? number_format($anggaran->anggaran, 0, ',', '.') : '0'
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
            'nominal' => number_format($nominal, 0, ',', '.')
        ]);
    }
}
