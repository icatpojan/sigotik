<?php

namespace App\Http\Controllers\Web\anggaran;

use Illuminate\Http\Request;
use App\Models\BbmAnggaranUpt;
use App\Models\BbmAnggaran;
use App\Models\MUpt;
use App\Models\ConfUser;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
class EntryAnggaranInternalController extends Controller
{
    public function index()
    {
        return view('anggaran.entry-anggaran-internal');
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $query = BbmAnggaranUpt::with(['upt', 'userInput'])
            ->where('m_upt_code', $user->m_upt_code)
            ->orderBy('tanggal_trans', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%")
                    ->orWhere('nominal', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function getFormData(Request $request)
    {
        $user = Auth::user();
        $upt = MUpt::where('code', $user->m_upt_code)->first();

        // Get anggaran terakhir untuk UPT ini
        $anggaran = BbmAnggaran::where('m_upt_code', $user->m_upt_code)
            ->where('statusanggaran', 1) // Hanya yang sudah disetujui
            ->orderBy('periode', 'desc')
            ->orderBy('perubahan_ke', 'desc')
            ->first();

        $data = [
            'upt' => $upt,
            'anggaran' => $anggaran ? $anggaran->anggaran : 0,
            'user' => $user
        ];

        return response()->json(['data' => $data]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'tanggal_trans' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'nomor_surat' => 'required|string|max:50',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            $user = Auth::user();

            $data = [
                'tanggal_trans' => $request->tanggal_trans,
                'm_upt_code' => $user->m_upt_code,
                'nominal' => $request->nominal,
                'nomor_surat' => $request->nomor_surat,
                'keterangan' => $request->keterangan,
                'statusperubahan' => 0, // Belum disetujui
                'user_input' => $user->username,
                'tanggal_input' => now()
            ];

            $anggaranUpt = BbmAnggaranUpt::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Anggaran internal berhasil disimpan',
                'data' => $anggaranUpt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan anggaran internal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getViewData($id)
    {
        $data = BbmAnggaranUpt::with(['upt', 'userInput', 'userApp'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function getEditForm($id)
    {
        $data = BbmAnggaranUpt::with(['upt'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'tanggal_trans' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'nomor_surat' => 'required|string|max:50',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            $anggaranUpt = BbmAnggaranUpt::find($request->id);

            if (!$anggaranUpt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggaran internal tidak ditemukan'
                ], 404);
            }

            // Cek apakah data bisa diedit (hanya yang belum disetujui)
            if ($anggaranUpt->statusperubahan != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang sudah disetujui tidak dapat diedit'
                ], 400);
            }

            $anggaranUpt->update([
                'tanggal_trans' => $request->tanggal_trans,
                'nominal' => $request->nominal,
                'nomor_surat' => $request->nomor_surat,
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anggaran internal berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui anggaran internal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $anggaranUpt = BbmAnggaranUpt::find($id);

            if (!$anggaranUpt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggaran internal tidak ditemukan'
                ], 404);
            }

            // Cek apakah data bisa dihapus (hanya yang belum disetujui)
            if ($anggaranUpt->statusperubahan != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang sudah disetujui tidak dapat dihapus'
                ], 400);
            }

            $anggaranUpt->delete();

            return response()->json([
                'success' => true,
                'message' => 'Anggaran internal berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus anggaran internal: ' . $e->getMessage()
            ], 500);
        }
    }
}
