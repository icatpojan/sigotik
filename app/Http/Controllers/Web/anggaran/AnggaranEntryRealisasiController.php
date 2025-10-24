<?php

namespace App\Http\Controllers\Web\anggaran;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BbmAnggaranUpt;
use App\Models\MUpt;
use App\Models\BbmAnggaran;
use App\Http\Controllers\Controller;

class AnggaranEntryRealisasiController extends Controller
{
    public function index()
    {
        return view('anggaran.entry-realisasi');
    }

    public function getData(Request $request)
    {
        $query = BbmAnggaranUpt::with('upt')
            ->select('bbm_anggaran_upt.*', 'm_upt.nama as upt_nama')
            ->join('m_upt', 'm_upt.code', '=', 'bbm_anggaran_upt.m_upt_code');

        // Apply filters
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m_upt.nama', 'like', "%{$search}%")
                    ->orWhere('bbm_anggaran_upt.nomor_surat', 'like', "%{$search}%")
                    ->orWhere('bbm_anggaran_upt.keterangan', 'like', "%{$search}%");
            });
        }

        if ($request->status) {
            $query->where('bbm_anggaran_upt.statusperubahan', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('bbm_anggaran_upt.tanggal_trans', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('bbm_anggaran_upt.tanggal_trans', '<=', $request->date_to);
        }

        $data = $query->orderBy('bbm_anggaran_upt.tanggal_trans', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'tanggal_trans' => 'required|date',
            'm_upt_code' => 'required|exists:m_upt,code',
            'nominal' => 'required|numeric|min:0',
            'nomor_surat' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:255'
        ]);

        try {

            DB::beginTransaction();
            $maxId=BbmAnggaranUpt::max('anggaran_upt_id') ?? 0;
            $newId=$maxId+1;

            $data = BbmAnggaranUpt::create([
                'anggaran_upt_id' => $newId,
                'tanggal_trans' => $request->tanggal_trans,
                'm_upt_code' => $request->m_upt_code,
                'nominal' => $request->nominal,
                'nomor_surat' => $request->nomor_surat,
                'keterangan' => $request->keterangan,
                'statusperubahan' => 0,
                'user_input' => auth()->user()->username ?? 'system',
                'tanggal_input' => now()->format('Y-m-d H:i:s')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data realisasi berhasil disimpan',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $data = BbmAnggaranUpt::with('upt')->find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:bbm_anggaran_upt,anggaran_upt_id',
            'tanggal_trans' => 'required|date',
            'm_upt_code' => 'required|exists:m_upt,code',
            'nominal' => 'required|numeric|min:0',
            'nomor_surat' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $data = BbmAnggaranUpt::find($request->id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $data->update([
                'tanggal_trans' => $request->tanggal_trans,
                'm_upt_code' => $request->m_upt_code,
                'nominal' => $request->nominal,
                'nomor_surat' => $request->nomor_surat,
                'keterangan' => $request->keterangan,
                'user_input' => auth()->user()->username ?? 'system',
                'tanggal_input' => now()->format('Y-m-d H:i:s')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data realisasi berhasil diperbarui',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function view($id)
    {
        $data = BbmAnggaranUpt::with('upt')->find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function delete($id)
    {
        try {
            $data = BbmAnggaranUpt::find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $data->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data realisasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAnggaran(Request $request)
    {
        $kodeUpt = $request->kode_upt;

        // Get latest approved anggaran for this UPT
        $anggaran = BbmAnggaran::where('m_upt_code', $kodeUpt)
            ->where('statusanggaran', 1)
            ->orderBy('periode', 'desc')
            ->orderBy('perubahan_ke', 'desc')
            ->first();

        return response()->json([
            'anggaran' => $anggaran ? $anggaran->anggaran : 0
        ]);
    }

    public function getNominalAwal(Request $request)
    {
        $tanggalTrans = $request->tanggal_trans;
        $kodeUpt = $request->kode_upt;

        // Get sum of nominal from realisasi before this date
        $nominal = BbmAnggaranUpt::where('m_upt_code', $kodeUpt)
            ->where('tanggal_trans', '<', $tanggalTrans)
            ->where('statusperubahan', 1)
            ->sum('nominal');

        return response()->json([
            'nominal' => $nominal
        ]);
    }
}
