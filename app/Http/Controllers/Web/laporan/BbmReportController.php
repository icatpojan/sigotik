<?php

namespace App\Http\Controllers\Web\laporan;

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
use App\Http\Controllers\Controller;

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
        $kapalId = $request->input('kapal_id');

        // Query sederhana menggunakan Eloquent - sesuai dengan project_ci
        $query = MKapal::with('upt')
            ->select([
                'm_kapal.m_kapal_id',
                'm_kapal.code_kapal',
                'm_kapal.nama_kapal',
                DB::raw('COALESCE(SUM(CASE WHEN bbm_kapaltrans.status_ba = 5 THEN bbm_transdetail.volume_isi ELSE 0 END), 0) as total_penerimaan'),
                DB::raw('COALESCE(SUM(CASE WHEN bbm_kapaltrans.status_ba = 3 THEN bbm_kapaltrans.volume_pemakaian ELSE 0 END), 0) as total_penggunaan')
            ])
            ->leftJoin('bbm_kapaltrans', 'm_kapal.code_kapal', '=', 'bbm_kapaltrans.kapal_code')
            ->leftJoin('bbm_transdetail', function ($join) {
                $join->on('bbm_kapaltrans.nomor_surat', '=', 'bbm_transdetail.nomor_surat')
                    ->where('bbm_kapaltrans.status_ba', '=', 5);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('bbm_kapaltrans.tanggal_surat', [$startDate, $endDate])
                    ->orWhereNull('bbm_kapaltrans.tanggal_surat');
            });

        // Filter UPT
        if ($uptId && $uptId != '0') {
            $query->where('m_kapal.m_upt_code', $uptId);
        }

        // Filter Kapal
        if ($kapalId && $kapalId != '0') {
            $query->where('m_kapal.m_kapal_id', $kapalId);
        }

        $query->groupBy('m_kapal.m_kapal_id', 'm_kapal.code_kapal', 'm_kapal.nama_kapal')
            ->havingRaw('total_penerimaan > 0 OR total_penggunaan > 0');

        $data = $query->get();

        // Hitung grand total
        $totalPenerimaan = $data->sum('total_penerimaan');
        $totalPenggunaan = $data->sum('total_penggunaan');

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
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query sederhana menggunakan Eloquent - sesuai dengan project_ci
        $query = MKapal::with('upt')
            ->select([
                'm_kapal.m_kapal_id',
                'm_kapal.code_kapal',
                'm_kapal.nama_kapal',
                DB::raw('COALESCE(SUM(CASE WHEN bbm_kapaltrans.status_ba = 5 THEN bbm_transdetail.volume_isi ELSE 0 END), 0) as total_penerimaan'),
                DB::raw('COALESCE(SUM(CASE WHEN bbm_kapaltrans.status_ba = 3 THEN bbm_kapaltrans.volume_pemakaian ELSE 0 END), 0) as total_penggunaan')
            ])
            ->leftJoin('bbm_kapaltrans', 'm_kapal.code_kapal', '=', 'bbm_kapaltrans.kapal_code')
            ->leftJoin('bbm_transdetail', function ($join) {
                $join->on('bbm_kapaltrans.nomor_surat', '=', 'bbm_transdetail.nomor_surat')
                    ->where('bbm_kapaltrans.status_ba', '=', 5);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('bbm_kapaltrans.tanggal_surat', [$startDate, $endDate])
                    ->orWhereNull('bbm_kapaltrans.tanggal_surat');
            });

        // Filter UPT
        if ($uptId && $uptId != '0') {
            $query->where('m_kapal.m_upt_code', $uptId);
        }

        // Filter Kapal
        if ($kapalId && $kapalId != '0') {
            $query->where('m_kapal.m_kapal_id', $kapalId);
        }

        $query->groupBy('m_kapal.m_kapal_id', 'm_kapal.code_kapal', 'm_kapal.nama_kapal')
            ->havingRaw('total_penerimaan > 0 OR total_penggunaan > 0');

        $data = $query->get();

        // Hitung grand total
        $totalPenerimaan = $data->sum('total_penerimaan');
        $totalPenggunaan = $data->sum('total_penggunaan');

        $formattedData = ExportHelper::formatDataForExport($data, 'total-penerimaan-penggunaan');

        // Tambahkan grand total
        if (!empty($formattedData)) {
            $formattedData[] = [
                'No' => '',
                'Nama Kapal' => 'GRAND TOTAL',
                'Total Penerimaan' => number_format($totalPenerimaan, 0, ',', '.') . ' Liter',
                'Total Penggunaan' => number_format($totalPenggunaan, 0, ',', '.') . ' Liter',
            ];
        }

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

        // Query sesuai dengan project_ci - menggunakan UNION untuk detail per transaksi
        $query = DB::table('m_kapal')
            ->join(DB::raw("
                (SELECT kapal_code, tanggal_surat, a.nomor_surat, SUM(volume_isi) AS penerimaan, 0 AS penggunaan
                FROM bbm_kapaltrans a, bbm_transdetail b
                WHERE a.status_ba = '5' AND a.nomor_surat = b.nomor_surat
                AND a.tanggal_surat >= '$startDate' AND a.tanggal_surat <= '$endDate'
                GROUP BY a.nomor_surat, a.kapal_code, a.tanggal_surat
                UNION
                SELECT kapal_code, tanggal_surat, nomor_surat, 0 AS penerimaan, volume_pemakaian AS penggunaan
                FROM bbm_kapaltrans
                WHERE status_ba = '3' AND tanggal_surat >= '$startDate' AND tanggal_surat <= '$endDate') trans
            "), 'm_kapal.code_kapal', '=', 'trans.kapal_code')
            ->select([
                'trans.tanggal_surat',
                'trans.nomor_surat',
                'trans.kapal_code',
                'm_kapal.nama_kapal',
                'trans.penerimaan',
                'trans.penggunaan'
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('trans.tanggal_surat', '>=', $startDate)
                    ->where('trans.tanggal_surat', '<=', $endDate);
            });

        // Filter UPT
        if ($uptId && $uptId != '0') {
            $query->where('m_kapal.m_upt_code', $uptId);
        }

        // Filter Kapal
        if ($kapalId && $kapalId != '0') {
            $query->where('m_kapal.m_kapal_id', $kapalId);
        }

        $query->orderBy('trans.tanggal_surat', 'asc')
            ->orderBy('trans.nomor_surat', 'asc')
            ->orderBy('trans.kapal_code', 'asc');

        $data = $query->get();

        // Hitung grand total
        $totalPenerimaan = $data->sum('penerimaan');
        $totalPenggunaan = $data->sum('penggunaan');

        return response()->json([
            'data' => $data,
            'total_penerimaan' => $totalPenerimaan,
            'total_penggunaan' => $totalPenggunaan
        ]);
    }

    public function exportDetailPenggunaanPenerimaan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query sesuai dengan project_ci - menggunakan UNION untuk detail per transaksi
        $query = DB::table('m_kapal')
            ->join(DB::raw("
                (SELECT kapal_code, tanggal_surat, a.nomor_surat, SUM(volume_isi) AS penerimaan, 0 AS penggunaan
                FROM bbm_kapaltrans a, bbm_transdetail b
                WHERE a.status_ba = '5' AND a.nomor_surat = b.nomor_surat
                AND a.tanggal_surat >= '$startDate' AND a.tanggal_surat <= '$endDate'
                GROUP BY a.nomor_surat, a.kapal_code, a.tanggal_surat
                UNION
                SELECT kapal_code, tanggal_surat, nomor_surat, 0 AS penerimaan, volume_pemakaian AS penggunaan
                FROM bbm_kapaltrans
                WHERE status_ba = '3' AND tanggal_surat >= '$startDate' AND tanggal_surat <= '$endDate') trans
            "), 'm_kapal.code_kapal', '=', 'trans.kapal_code')
            ->select([
                'trans.tanggal_surat',
                'trans.nomor_surat',
                'trans.kapal_code',
                'm_kapal.nama_kapal',
                'trans.penerimaan',
                'trans.penggunaan'
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('trans.tanggal_surat', '>=', $startDate)
                    ->where('trans.tanggal_surat', '<=', $endDate);
            });

        // Filter UPT
        if ($uptId && $uptId != '0') {
            $query->where('m_kapal.m_upt_code', $uptId);
        }

        // Filter Kapal
        if ($kapalId && $kapalId != '0') {
            $query->where('m_kapal.m_kapal_id', $kapalId);
        }

        $query->orderBy('trans.tanggal_surat', 'asc')
            ->orderBy('trans.nomor_surat', 'asc')
            ->orderBy('trans.kapal_code', 'asc');

        $data = $query->get();

        // Hitung grand total
        $totalPenerimaan = $data->sum('penerimaan');
        $totalPenggunaan = $data->sum('penggunaan');

        $formattedData = ExportHelper::formatDataForExport($data, 'detail-penggunaan-penerimaan');

        // Tambahkan grand total
        if (!empty($formattedData)) {
            $formattedData[] = [
                'No' => '',
                'Tanggal BA' => '',
                'Nomor BA' => '',
                'Nama Kapal' => 'GRAND TOTAL',
                'Total Penerimaan' => number_format($totalPenerimaan, 0, ',', '.') . ' Liter',
                'Total Penggunaan' => number_format($totalPenggunaan, 0, ',', '.') . ' Liter',
            ];
        }

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

        // Query sesuai dengan CodeIgniter - menggunakan UNION untuk menggabungkan penerimaan dan penggunaan
        $query = DB::table('m_kapal')
            ->join('m_upt', 'm_kapal.m_upt_code', '=', 'm_upt.code')
            ->join(DB::raw("
                (SELECT kapal_code, tanggal_surat, a.nomor_surat, a.keterangan_jenis_bbm, SUM(volume_isi) AS penerimaan, 0 AS penggunaan
                FROM bbm_kapaltrans a, bbm_transdetail b
                WHERE a.status_ba = '5' AND a.nomor_surat = b.nomor_surat
                AND a.tanggal_surat >= '$startDate' AND a.tanggal_surat <= '$endDate'
                GROUP BY a.nomor_surat, a.kapal_code, a.tanggal_surat, a.keterangan_jenis_bbm
                UNION
                SELECT kapal_code, tanggal_surat, nomor_surat, keterangan_jenis_bbm, 0 AS penerimaan, volume_pemakaian AS penggunaan
                FROM bbm_kapaltrans
                WHERE status_ba = '3' AND tanggal_surat >= '$startDate' AND tanggal_surat <= '$endDate') trans
            "), 'm_kapal.code_kapal', '=', 'trans.kapal_code')
            ->select([
                'trans.tanggal_surat',
                'trans.nomor_surat',
                'trans.kapal_code',
                'trans.keterangan_jenis_bbm',
                'm_kapal.nama_kapal',
                'm_upt.nama as upt_nama',
                'trans.penerimaan',
                'trans.penggunaan'
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('trans.tanggal_surat', '>=', $startDate)
                    ->where('trans.tanggal_surat', '<=', $endDate);
            });

        // Filter UPT
        if ($uptId && $uptId != '0') {
            $query->where('m_kapal.m_upt_code', $uptId);
        }

        // Filter Kapal
        if ($kapalId && $kapalId != '0') {
            $query->where('m_kapal.m_kapal_id', $kapalId);
        }

        $data = $query->orderBy('trans.tanggal_surat', 'desc')->get();

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            // Tentukan jenis transaksi berdasarkan volume
            $jenisTransaksi = $item->penerimaan > 0 ? 'Penerimaan' : 'Penggunaan';
            $volume = $item->penerimaan > 0 ? $item->penerimaan : $item->penggunaan;

            return [
                'id' => $item->nomor_surat,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->upt_nama ?: '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->nama_kapal ?: '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $volume,
                'status' => $jenisTransaksi,
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    private function getJenisTransaksi($statusBa)
    {
        $statusMap = [
            1 => 'Akhir Bulan',
            2 => 'Sebelum Pengisian',
            3 => 'Penggunaan',
            4 => 'Pemeriksaan Sarana',
            5 => 'Penerimaan',
            6 => 'Sebelum Pelayaran',
            7 => 'Sesudah Pelayaran',
            8 => 'Penitipan',
            9 => 'Pengembalian'
        ];

        return $statusMap[$statusBa] ?? 'Unknown';
    }

    private function getVolumeForStatus($item)
    {
        switch ($item->status_ba) {
            case 2: // Sebelum Pengisian
                return $item->volume_sisa ?: 0;
            case 3: // Penggunaan
                return $item->volume_pemakaian ?: 0;
            case 5: // Penerimaan
                return $item->volume_pengisian ?: 0;
            case 6: // Sebelum Pelayaran
                return $item->volume_sisa ?: 0;
            case 7: // Sesudah Pelayaran
                return $item->volume_sisa ?: 0;
            default:
                return $item->volume_sisa ?: 0;
        }
    }

    public function exportHistoryPenerimaanPenggunaan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query sesuai dengan CodeIgniter - menggunakan UNION untuk menggabungkan penerimaan dan penggunaan
        $query = DB::table('m_kapal')
            ->join('m_upt', 'm_kapal.m_upt_code', '=', 'm_upt.code')
            ->join(DB::raw("
                (SELECT kapal_code, tanggal_surat, a.nomor_surat, a.keterangan_jenis_bbm, SUM(volume_isi) AS penerimaan, 0 AS penggunaan
                FROM bbm_kapaltrans a, bbm_transdetail b
                WHERE a.status_ba = '5' AND a.nomor_surat = b.nomor_surat
                AND a.tanggal_surat >= '$startDate' AND a.tanggal_surat <= '$endDate'
                GROUP BY a.nomor_surat, a.kapal_code, a.tanggal_surat, a.keterangan_jenis_bbm
                UNION
                SELECT kapal_code, tanggal_surat, nomor_surat, keterangan_jenis_bbm, 0 AS penerimaan, volume_pemakaian AS penggunaan
                FROM bbm_kapaltrans
                WHERE status_ba = '3' AND tanggal_surat >= '$startDate' AND tanggal_surat <= '$endDate') trans
            "), 'm_kapal.code_kapal', '=', 'trans.kapal_code')
            ->select([
                'trans.tanggal_surat',
                'trans.nomor_surat',
                'trans.kapal_code',
                'trans.keterangan_jenis_bbm',
                'm_kapal.nama_kapal',
                'm_upt.nama as upt_nama',
                'trans.penerimaan',
                'trans.penggunaan'
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('trans.tanggal_surat', '>=', $startDate)
                    ->where('trans.tanggal_surat', '<=', $endDate);
            });

        // Filter UPT
        if ($uptId && $uptId != '0') {
            $query->where('m_kapal.m_upt_code', $uptId);
        }

        // Filter Kapal
        if ($kapalId && $kapalId != '0') {
            $query->where('m_kapal.m_kapal_id', $kapalId);
        }

        $data = $query->orderBy('trans.tanggal_surat', 'desc')->get();

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            // Tentukan jenis transaksi berdasarkan volume
            $jenisTransaksi = $item->penerimaan > 0 ? 'Penerimaan' : 'Penggunaan';
            $volume = $item->penerimaan > 0 ? $item->penerimaan : $item->penggunaan;

            return [
                'Tanggal Transaksi' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->upt_nama ?: '-',
                'Kapal' => $item->nama_kapal ?: '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume (Liter)' => $volume,
                'Status' => $jenisTransaksi,
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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
            ->where('status_ba', 1) // BA Akhir Bulan (sesuai CodeIgniter)
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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $item->volume_pemakaian ?: 0,
                'status' => 'BA Akhir Bulan',
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function exportAkhirBulan(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $uptId = $request->input('upt_id');
        $format = $request->input('format', 'excel'); // excel or pdf

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

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Tanggal' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume Pemakaian (Liter)' => $item->volume_pemakaian ?: 0,
                'Status' => 'BA Akhir Bulan',
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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

        $query = BbmKapaltrans::with(['kapal.upt', 'transdetails'])
            ->where('status_ba', 5) // BA Penerimaan BBM (sesuai CodeIgniter)
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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            // Hitung total volume dari transdetails
            $totalVolume = 0;
            if ($item->transdetails && $item->transdetails->count() > 0) {
                $totalVolume = $item->transdetails->sum('volume_isi');
            }

            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $totalVolume,
                'status' => 'BA Penerimaan BBM',
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function exportPenerimaan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        $query = BbmKapaltrans::with(['kapal.upt', 'transdetails'])
            ->where('status_ba', 5) // BA Penerimaan BBM
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

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            // Hitung total volume dari transdetails
            $totalVolume = 0;
            if ($item->transdetails && $item->transdetails->count() > 0) {
                $totalVolume = $item->transdetails->sum('volume_isi');
            }

            return [
                'Tanggal Transaksi' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume (Liter)' => $totalVolume,
                'Status' => 'BA Penerimaan BBM',
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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
            ->where('status_ba', 8) // BA Penitipan BBM (sesuai CodeIgniter)
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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $item->penggunaan ?: 0, // Volume penitipan dari field 'penggunaan'
                'status' => 'BA Penitipan BBM',
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function exportPenitipan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

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

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Tanggal Transaksi' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume Penitipan (Liter)' => $item->penggunaan ?: 0,
                'Status' => 'BA Penitipan BBM',
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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
            ->where('status_ba', 9) // BA Pengembalian BBM (sesuai CodeIgniter)
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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $item->penggunaan ?: 0, // Volume pengembalian dari field 'penggunaan'
                'status' => 'BA Pengembalian BBM',
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function exportPengembalian(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

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

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Tanggal Transaksi' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume Pengembalian (Liter)' => $item->penggunaan ?: 0,
                'Status' => 'BA Pengembalian BBM',
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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
            ->where('status_ba', 10) // BA Peminjaman BBM (sesuai CodeIgniter)
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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $item->penggunaan ?: 0, // Volume peminjaman dari field 'penggunaan'
                'status' => 'BA Peminjaman BBM',
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function exportPeminjaman(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

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

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Tanggal Transaksi' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume Peminjaman (Liter)' => $item->penggunaan ?: 0,
                'Status' => 'BA Peminjaman BBM',
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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
            ->where('status_ba', 12) // BA Pengembalian Pinjaman BBM (sesuai CodeIgniter)
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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $item->penggunaan ?: 0, // Volume pengembalian pinjaman dari field 'penggunaan'
                'status' => 'BA Pengembalian Pinjaman BBM',
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function exportPengembalianPinjaman(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

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

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Tanggal Transaksi' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume Pengembalian Pinjaman (Liter)' => $item->penggunaan ?: 0,
                'Status' => 'BA Pengembalian Pinjaman BBM',
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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
                    ->whereRaw('bbm2.kapal_code = bbm_kapaltrans.kapal_code')
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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $item->penggunaan ?: 0, // Volume pinjaman dari field 'penggunaan'
                'status' => 'Belum Dikembalikan',
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function exportPinjamanBelumDikembalikan(Request $request)
    {
        $uptId = $request->input('upt_id');
        $kapalId = $request->input('kapal_id');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query untuk pinjaman yang belum dikembalikan
        $query = BbmKapaltrans::with(['kapal.upt'])
            ->where('status_ba', 10) // BA Peminjaman BBM
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('bbm_kapaltrans as bbm2')
                    ->whereRaw('bbm2.kapal_code = bbm_kapaltrans.kapal_code')
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

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Tanggal Peminjaman' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                'Jenis BBM' => $item->keterangan_jenis_bbm ?: '-',
                'Volume Pinjaman (Liter)' => $item->penggunaan ?: 0,
                'Status' => 'Belum Dikembalikan',
                'Nomor Surat' => $item->nomor_surat ?: '-'
            ];
        });

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

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            $statusText = $item->status_ba == 14 ? 'BA Pemberian Hibah BBM Antar Kapal Pengawas' : 'BA Penerimaan Hibah BBM Antar Kapal Pengawas';

            return [
                'id' => $item->trans_id,
                'tgl_trans' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'upt' => [
                    'nama_upt' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-'
                ],
                'kapal' => [
                    'nama_kapal' => $item->kapal ? $item->kapal->nama_kapal : '-'
                ],
                'jenis_bbm' => $item->keterangan_jenis_bbm ?: '-',
                'jumlah' => $item->penggunaan ?: 0, // Volume hibah dari field 'penggunaan'
                'status' => $statusText,
                'keterangan' => $item->nomor_surat ?: '-'
            ];
        });

        return response()->json(['data' => $formattedData]);
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
        $upts = MUpt::select('m_upt_id as id', 'nama as nama_upt')
            ->orderBy('nama')
            ->get();

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
