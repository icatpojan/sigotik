<?php

namespace App\Http\Controllers\Web\anggaran;

use Illuminate\Http\Request;
use App\Models\BbmTagihan;
use App\Models\MUpt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class TanggalSppdController extends Controller
{
    public function index()
    {
        return view('anggaran.tanggal-sppd');
    }

    public function getData(Request $request)
    {
        $query = BbmTagihan::with(['upt'])
            ->where('statustagihan', 1) // Hanya data yang sudah disetujui
            ->orderBy('tanggal_invoice', 'desc');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tagihan', 'LIKE', "%{$search}%")
                    ->orWhere('penyedia', 'LIKE', "%{$search}%")
                    ->orWhere('m_upt_code', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function viewTagihan($id)
    {
        $data = BbmTagihan::with(['upt'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function getFormInputTanggal($id)
    {
        $data = BbmTagihan::with(['upt'])->find($id);
        return response()->json(['data' => $data]);
    }

    public function updateTanggalSppd(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'tanggal_sppd' => 'required|date'
        ]);

        try {
            $tagihan = BbmTagihan::find($request->id);
            if (!$tagihan) {
                return response()->json(['success' => false, 'message' => 'Data tagihan tidak ditemukan']);
            }

            $tagihan->update([
                'tanggal_sppd' => $request->tanggal_sppd
            ]);

            return response()->json(['success' => true, 'message' => 'Tanggal SPPD berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui tanggal SPPD: ' . $e->getMessage()]);
        }
    }

    public function getUploadForm($id)
    {
        $data = BbmTagihan::find($id);
        return response()->json(['data' => $data]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|integer',
            'file_sppd' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            $tagihan = BbmTagihan::find($request->tagihan_id);
            if (!$tagihan) {
                return response()->json(['success' => false, 'message' => 'Data tagihan tidak ditemukan']);
            }

            // Hapus file lama jika ada
            if ($tagihan->file_sppd) {
                Storage::disk('public')->delete('dokumen_sppd/' . $tagihan->file_sppd);
            }

            // Upload file baru
            $file = $request->file('file_sppd');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('dokumen_sppd', $filename, 'public');

            $tagihan->update([
                'file_sppd' => $filename
            ]);

            return response()->json(['success' => true, 'message' => 'File SPPD berhasil diupload']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal upload file: ' . $e->getMessage()]);
        }
    }

    public function downloadFile($filename)
    {
        $filePath = storage_path('app/public/dokumen_sppd/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath);
    }

    public function cancelTagihan(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        try {
            $tagihan = BbmTagihan::find($request->id);
            if (!$tagihan) {
                return response()->json(['success' => false, 'message' => 'Data tagihan tidak ditemukan']);
            }

            $tagihan->update([
                'statustagihan' => 2, // Status batal
                'user_batal' => Auth::user()->username ?? 'admin',
                'tanggal_batal' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Tagihan berhasil dibatalkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan tagihan: ' . $e->getMessage()]);
        }
    }
}
