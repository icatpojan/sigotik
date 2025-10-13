<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BbmKapaltrans;
use App\Models\BbmAnggaran;
use App\Models\BbmTagihan;
use App\Models\BbmTransdetail;
use App\Models\MKapal;
use App\Models\MUpt;
use Carbon\Carbon;
use App\Helpers\ExportHelper;

class BbmReportController extends Controller
{
    /**
     * LAP Total Penerimaan & Penggunaan BBM
     */
    public function totalPenerimaanPenggunaan()
    {
        return view('laporan-bbm.total-penerimaan-penggunaan');
    }

    public function getTotalPenerimaanPenggunaanData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select([
                'bbm_kapaltrans.*',
                DB::raw('CASE WHEN status_ba = 5 THEN volume_pengisian ELSE 0 END as total_penerimaan'),
                DB::raw('CASE WHEN status_ba = 3 THEN volume_pemakaian ELSE 0 END as total_penggunaan')
            ]);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->get();

        // Hitung total penerimaan dan penggunaan
        $totalPenerimaan = $data->where('status_ba', 5)->sum('volume_pengisian');
        $totalPenggunaan = $data->where('status_ba', 3)->sum('volume_pemakaian');

        return response()->json([
            'data' => $data,
            'total_penerimaan' => $totalPenerimaan,
            'total_penggunaan' => $totalPenggunaan
        ]);
    }

    public function exportTotalPenerimaanPenggunaan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select([
                'bbm_kapaltrans.*',
                DB::raw('CASE WHEN status_ba = 5 THEN volume_pengisian ELSE 0 END as total_penerimaan'),
                DB::raw('CASE WHEN status_ba = 3 THEN volume_pemakaian ELSE 0 END as total_penggunaan')
            ]);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'total-penerimaan-penggunaan');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Total Penerimaan & Penggunaan BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * LAP Detail Penggunaan & Penerimaan BBM
     */
    public function detailPenggunaanPenerimaan()
    {
        return view('laporan-bbm.detail-penggunaan-penerimaan');
    }

    public function getDetailPenggunaanPenerimaanData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        $query = BbmKapaltrans::with(['kapal.upt', 'transdetails'])
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportDetailPenggunaanPenerimaan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'detail-penggunaan-penerimaan');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Detail Penggunaan & Penerimaan BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * History Penerimaan & Penggunaan BBM
     */
    public function historyPenerimaanPenggunaan()
    {
        return view('laporan-bbm.history-penerimaan-penggunaan');
    }

    public function getHistoryPenerimaanPenggunaanData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportHistoryPenerimaanPenggunaan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'history-penerimaan-penggunaan');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP History Penerimaan & Penggunaan BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan BBM Akhir Bulan
     */
    public function akhirBulan()
    {
        return view('laporan-bbm.akhir-bulan');
    }

    public function getAkhirBulanData(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $uptId = $request->input('upt_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 1) // BA Akhir Bulan
            ->select('bbm_kapaltrans.*');

        if ($bulan && $tahun) {
            $query->whereMonth('tanggal_surat', $bulan)
                ->whereYear('tanggal_surat', $tahun);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportAkhirBulan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 1);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'akhir-bulan');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Akhir Bulan';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Penerimaan BBM
     */
    public function penerimaan()
    {
        return view('laporan-bbm.penerimaan');
    }

    public function getPenerimaanData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 1) // Penerimaan
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPenerimaan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 5);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'penerimaan');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Penerimaan BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Penitipan BBM
     */
    public function penitipan()
    {
        return view('laporan-bbm.penitipan');
    }

    public function getPenitipanData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 8) // BA Penitipan BBM
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPenitipan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 8);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'penitipan');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Penitipan BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Pengembalian BBM
     */
    public function pengembalian()
    {
        return view('laporan-bbm.pengembalian');
    }

    public function getPengembalianData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 9) // BA Pengembalian BBM
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPengembalian(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 9);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'pengembalian');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Pengembalian BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Peminjaman
     */
    public function peminjaman()
    {
        return view('laporan-bbm.peminjaman');
    }

    public function getPeminjamanData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 10) // BA Peminjaman BBM
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPeminjaman(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 10);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'peminjaman');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Peminjaman BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Pengembalian Pinjaman
     */
    public function pengembalianPinjaman()
    {
        return view('laporan-bbm.pengembalian-pinjaman');
    }

    public function getPengembalianPinjamanData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 12) // BA Pengembalian Pinjaman BBM
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPengembalianPinjaman(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 12);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'pengembalian-pinjaman');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Pengembalian Pinjaman BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Pinjaman Belum dikembalikan
     */
    public function pinjamanBelumDikembalikan()
    {
        return view('laporan-bbm.pinjaman-belum-dikembalikan');
    }

    public function getPinjamanBelumDikembalikanData(Request $request)
    {
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');

        // Query untuk pinjaman yang belum dikembalikan
        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 10) // BA Peminjaman BBM
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('bbm_kapaltrans as bbm2')
                    ->whereRaw('bbm2.kapal_id = bbm_kapaltrans.kapal_id')
                    ->where('bbm2.status_ba', 12); // BA Pengembalian Pinjaman BBM
            })
            ->select('bbm_kapaltrans.*');

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        if ($kapalId) {
            $query->whereHas('kapal', function ($q) use ($kapalId) {
                $q->where('m_kapal_id', $kapalId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPinjamanBelumDikembalikan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 10);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'pinjaman-belum-dikembalikan');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Pinjaman Belum Dikembalikan';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Hibah Antar Kapal Pengawas
     */
    public function hibahAntarKapalPengawas()
    {
        return view('laporan-bbm.hibah-antar-kapal-pengawas');
    }

    public function getHibahAntarKapalPengawasData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->whereIn('status_ba', [14, 15]) // BA Pemberian/Penerimaan Hibah BBM Antar Kapal Pengawas
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportHibahAntarKapalPengawas(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->whereIn('status_ba', [14, 15]);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'hibah-antar-kapal-pengawas');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Hibah Antar Kapal Pengawas';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Pemberi Hibah BBM Instansi Lain
     */
    public function pemberiHibahInstansiLain()
    {
        return view('laporan-bbm.pemberi-hibah-instansi-lain');
    }

    public function getPemberiHibahInstansiLainData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 16) // BA Pemberian Hibah BBM Instansi Lain
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPemberiHibahInstansiLain(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 16);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'pemberi-hibah-instansi-lain');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Pemberi Hibah Instansi Lain';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Penerima Hibah BBM Instansi Lain
     */
    public function penerimaHibahInstansiLain()
    {
        return view('laporan-bbm.penerima-hibah-instansi-lain');
    }

    public function getPenerimaHibahInstansiLainData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 18) // BA Penerimaan Hibah BBM Instansi Lain
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPenerimaHibahInstansiLain(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 18);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'penerima-hibah-instansi-lain');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Penerima Hibah Instansi Lain';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Laporan Penerimaan Hibah BBM
     */
    public function penerimaanHibah()
    {
        return view('laporan-bbm.penerimaan-hibah');
    }

    public function getPenerimaanHibahData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 17) // BA Penerimaan Hibah BBM
            ->select('bbm_kapaltrans.*');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function exportPenerimaanHibah(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt'])
            ->select('bbm_kapaltrans.*')->where('status_ba', 17);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        if ($uptId) {
            $query->whereHas('kapal', function ($q) use ($uptId) {
                $q->where('m_upt_code', $uptId);
            });
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();
        $formattedData = ExportHelper::formatDataForExport($data, 'penerimaan-hibah');

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Penerimaan Hibah BBM';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    /**
     * Helper methods untuk dropdown data
     */
    public function getUptOptions()
    {
        $upts = MUpt::select('m_upt_id as id', 'nama as nama_upt')->get();
        return response()->json($upts);
    }

    public function getKapalOptions(Request $request)
    {
        $uptId = $request->input('upt_id');
        $query = MKapal::select('m_kapal_id as id', 'nama_kapal');

        if ($uptId) {
            $query->where('m_upt_code', $uptId);
        }

        $kapals = $query->get();
        return response()->json($kapals);
    }
}
