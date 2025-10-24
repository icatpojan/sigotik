<?php

namespace App\Http\Controllers\Web\laporan;

use Illuminate\Http\Request;
use App\Models\BbmAnggaran;
use App\Models\BbmAnggaranUpt;
use App\Models\BbmTagihan;
use App\Models\BbmTransdetail;
use App\Models\BbmKapaltrans;
use App\Models\MUpt;
use App\Models\MKapal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Helpers\ExportHelper;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    // 1. Laporan Anggaran
    public function anggaran()
    {
        return view('laporan.anggaran');
    }

    public function getAnggaranData(Request $request)
    {
        $periode = $request->input('periode');

        $query = BbmAnggaran::with(['upt'])
            ->select('periode', 'm_upt_code', DB::raw('SUM(anggaran) as total_anggaran'))
            ->where('perubahan_ke', 0);

        if ($periode) {
            $query->where('periode', $periode);
        }

        $data = $query->groupBy('periode', 'm_upt_code')
            ->orderBy('periode', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $data]);
        }

        return $data;
    }

    public function getPeriodeOptions()
    {
        $periodes = BbmAnggaran::select('periode')
            ->groupBy('periode')
            ->orderBy('periode', 'desc')
            ->get();

        return response()->json(['data' => $periodes]);
    }

    // 2. Riwayat Anggaran & Realisasi ALL
    public function riwayatAll()
    {
        return view('laporan.riwayat-all');
    }

    public function getRiwayatAllData(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        // Query yang sesuai dengan CodeIgniter - menggabungkan data anggaran dan tagihan
        $query = DB::table('bbm_anggaran')
            ->join('m_upt', 'm_upt.code', '=', 'bbm_anggaran.m_upt_code')
            ->select(
                'bbm_anggaran.periode',
                'bbm_anggaran.m_upt_code',
                'm_upt.nama as nama_upt',
                'bbm_anggaran.anggaran',
                DB::raw('(
                    SELECT IF(SUM(total) IS NULL, 0, SUM(total))
                    FROM bbm_tagihan
                    WHERE m_upt_code = bbm_anggaran.m_upt_code
                    AND statustagihan = 1
                    AND tanggal_invoice BETWEEN "' . $tglAwal . '" AND "' . $tglAkhir . '"
                ) as total_tagihan')
            )
            ->where('bbm_anggaran.statusanggaran', 1);

        if ($tglAwal && $tglAkhir) {
            // Filter berdasarkan periode anggaran yang sesuai dengan tanggal
            $tahun = date('Y', strtotime($tglAwal));
            $query->where('bbm_anggaran.periode', $tahun);
        }

        // Ambil perubahan_ke terakhir untuk setiap UPT
        $maxPerubahanKe = DB::table('bbm_anggaran')
            ->select('m_upt_code', DB::raw('MAX(perubahan_ke) as max_perubahan_ke'))
            ->where('statusanggaran', 1)
            ->groupBy('m_upt_code');

        $query->joinSub($maxPerubahanKe, 'max_perubahan', function ($join) {
            $join->on('bbm_anggaran.m_upt_code', '=', 'max_perubahan.m_upt_code')
                ->on('bbm_anggaran.perubahan_ke', '=', 'max_perubahan.max_perubahan_ke');
        });

        $data = $query->orderBy('bbm_anggaran.m_upt_code', 'asc')->get();

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'periode' => $item->periode,
                'm_upt_code' => $item->m_upt_code,
                'nama_upt' => $item->nama_upt,
                'anggaran' => $item->anggaran,
                'total_tagihan' => $item->total_tagihan,
                'sisa_anggaran' => $item->anggaran - $item->total_tagihan
            ];
        });

        if ($request->expectsJson()) {
            return response()->json(['data' => $formattedData]);
        }

        return $formattedData;
    }

    // 3. Laporan Realisasi per Periode
    public function realisasiPeriode()
    {
        return view('laporan.realisasi-periode');
    }

    public function getRealisasiPeriodeData(Request $request)
    {
        $periode = $request->input('periode');
        $uptCode = $request->input('upt_code');

        // Query yang sesuai dengan CodeIgniter - menggunakan bbm_tagihan dengan kondisi yang tepat
        $query = BbmTagihan::with(['upt'])
            ->select(
                'm_upt_code',
                'no_tagihan',
                'tanggal_invoice',
                'quantity',
                'total',
                DB::raw('total/quantity as rata_harga'),
                DB::raw('YEAR(tanggal_invoice) as periode')
            )
            ->where('statustagihan', 1)
            ->whereNotNull('tanggal_sppd'); // Hanya data yang sudah ada tanggal SP2D

        if ($periode) {
            $query->whereYear('tanggal_invoice', $periode);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        $data = $query->orderBy('tanggal_invoice', 'desc')->get();

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'periode' => $item->periode,
                'upt' => [
                    'nama' => $item->upt ? $item->upt->nama : '-'
                ],
                'no_tagihan' => $item->no_tagihan,
                'tanggal_surat' => $item->tanggal_invoice ? \Carbon\Carbon::parse($item->tanggal_invoice)->locale('id')->format('d F Y') : '-',
                'total_realisasi' => $item->total,
                'quantity' => $item->quantity,
                'rata_harga' => $item->rata_harga
            ];
        });

        if ($request->expectsJson()) {
            return response()->json(['data' => $formattedData]);
        }

        return $formattedData;
    }

    public function exportRealisasiPeriode(Request $request)
    {
        $periode = $request->input('periode');
        $uptCode = $request->input('upt_code');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query yang sama dengan getRealisasiPeriodeData
        $query = BbmTagihan::with(['upt'])
            ->select(
                'm_upt_code',
                'no_tagihan',
                'tanggal_invoice',
                'quantity',
                'total',
                DB::raw('total/quantity as rata_harga'),
                DB::raw('YEAR(tanggal_invoice) as periode')
            )
            ->where('statustagihan', 1)
            ->whereNotNull('tanggal_sppd');

        if ($periode) {
            $query->whereYear('tanggal_invoice', $periode);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        $data = $query->orderBy('tanggal_invoice', 'desc')->get();

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Periode' => $item->periode,
                'UPT' => $item->upt ? $item->upt->nama : '-',
                'No Tagihan' => $item->no_tagihan,
                'Tanggal Invoice' => $item->tanggal_invoice ? \Carbon\Carbon::parse($item->tanggal_invoice)->locale('id')->format('d F Y') : '-',
                'Quantity (Liter)' => $item->quantity,
                'Total Realisasi (Rp)' => $item->total,
                'Rata-rata Harga (Rp)' => number_format($item->rata_harga, 2, ',', '.')
            ];
        });

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Realisasi per Periode';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    public function getUptOptions()
    {
        $upts = MUpt::select('code', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json(['data' => $upts]);
    }

    // 4. Laporan Transaksi Realisasi UPT
    public function transaksiRealisasiUpt()
    {
        return view('laporan.transaksi-realisasi-upt');
    }

    public function getTransaksiRealisasiUptData(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');
        $uptCode = $request->input('upt_code');
        $noTagihan = $request->input('no_tagihan');

        // Query yang sesuai dengan CodeIgniter - menggunakan bbm_tagihan dengan kondisi yang tepat
        $query = BbmTagihan::with(['upt'])
            ->select(
                'm_upt_code',
                'no_tagihan',
                'tanggal_invoice',
                'quantity',
                'total',
                DB::raw('total/quantity as rata_harga'),
                DB::raw('YEAR(tanggal_invoice) as periode')
            )
            ->where('statustagihan', 1)
            ->whereNotNull('tanggal_sppd'); // Hanya data yang sudah ada tanggal SP2D

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tanggal_invoice', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        if ($noTagihan) {
            $query->where('no_tagihan', 'like', '%' . $noTagihan . '%');
        }

        $data = $query->orderBy('tanggal_invoice', 'desc')->get();

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'periode' => $item->periode,
                'upt' => [
                    'nama' => $item->upt ? $item->upt->nama : '-'
                ],
                'no_tagihan' => $item->no_tagihan,
                'tanggal_surat' => $item->tanggal_invoice ? \Carbon\Carbon::parse($item->tanggal_invoice)->locale('id')->format('d F Y') : '-',
                'total_realisasi' => $item->total,
                'quantity' => $item->quantity,
                'rata_harga' => $item->rata_harga
            ];
        });

        if ($request->expectsJson()) {
            return response()->json(['data' => $formattedData]);
        }

        return $formattedData;
    }

    public function exportTransaksiRealisasiUpt(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');
        $uptCode = $request->input('upt_code');
        $noTagihan = $request->input('no_tagihan');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query yang sama dengan getTransaksiRealisasiUptData
        $query = BbmTagihan::with(['upt'])
            ->select(
                'm_upt_code',
                'no_tagihan',
                'tanggal_invoice',
                'quantity',
                'total',
                DB::raw('total/quantity as rata_harga'),
                DB::raw('YEAR(tanggal_invoice) as periode')
            )
            ->where('statustagihan', 1)
            ->whereNotNull('tanggal_sppd');

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tanggal_invoice', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        if ($noTagihan) {
            $query->where('no_tagihan', 'like', '%' . $noTagihan . '%');
        }

        $data = $query->orderBy('tanggal_invoice', 'desc')->get();

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'Periode' => $item->periode,
                'UPT' => $item->upt ? $item->upt->nama : '-',
                'No Tagihan' => $item->no_tagihan,
                'Tanggal Invoice' => $item->tanggal_invoice ? \Carbon\Carbon::parse($item->tanggal_invoice)->locale('id')->format('d F Y') : '-',
                'Quantity (Liter)' => $item->quantity,
                'Total Realisasi (Rp)' => $item->total,
                'Rata-rata Harga (Rp)' => number_format($item->rata_harga, 2, ',', '.')
            ];
        });

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Transaksi Realisasi UPT';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    public function getNoTagihanOptions(Request $request)
    {
        $uptCode = $request->input('upt_code');

        $query = BbmTagihan::select('no_tagihan')
            ->where('statustagihan', 1);

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        $data = $query->groupBy('no_tagihan')
            ->orderBy('no_tagihan')
            ->get();

        return response()->json(['data' => $data]);
    }

    // 5. Laporan Transaksi Perubahan Anggaran Internal UPT
    public function perubahanAnggaranInternal()
    {
        return view('laporan.perubahan-anggaran-internal');
    }

    public function getPerubahanAnggaranInternalData(Request $request)
    {
        $periode = $request->input('periode');
        $uptCode = $request->input('upt_code');

        // Query yang sesuai dengan CodeIgniter - menggunakan bbm_anggaran_upt dengan join m_upt
        $query = BbmAnggaranUpt::with(['upt'])
            ->select('bbm_anggaran_upt.*', 'm_upt.nama')
            ->join('m_upt', 'm_upt.code', '=', 'bbm_anggaran_upt.m_upt_code')
            ->whereIn('bbm_anggaran_upt.statusperubahan', [0, 2]);

        if ($uptCode) {
            $query->where('bbm_anggaran_upt.m_upt_code', $uptCode);
        }

        $data = $query->orderBy('bbm_anggaran_upt.tanggal_trans', 'desc')->get();

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            $statusText = '';
            if ($item->statusperubahan == 0) {
                $statusText = 'Belum Di Setujui';
            } elseif ($item->statusperubahan == 2) {
                $statusText = 'Pengajuan Dibatalkan';
            } else {
                $statusText = 'Sudah Di Setujui';
            }

            return [
                'id' => $item->anggaran_upt_id,
                'upt' => [
                    'nama' => $item->upt ? $item->upt->nama : '-'
                ],
                'tanggal_trans' => $item->tanggal_trans ? \Carbon\Carbon::parse($item->tanggal_trans)->locale('id')->format('d F Y') : '-',
                'nominal' => $item->nominal,
                'nomor_surat' => $item->nomor_surat ?: '-',
                'keterangan' => $item->keterangan ?: '-',
                'status' => $statusText,
                'user_input' => $item->user_input ?: '-',
                'tanggal_input' => $item->tanggal_input ? \Carbon\Carbon::parse($item->tanggal_input)->locale('id')->format('d F Y H:i') : '-'
            ];
        });

        if ($request->expectsJson()) {
            return response()->json(['data' => $formattedData]);
        }

        return $formattedData;
    }

    public function exportPerubahanAnggaranInternal(Request $request)
    {
        $uptCode = $request->input('upt_code');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query yang sama dengan getPerubahanAnggaranInternalData
        $query = BbmAnggaranUpt::with(['upt'])
            ->select('bbm_anggaran_upt.*', 'm_upt.nama')
            ->join('m_upt', 'm_upt.code', '=', 'bbm_anggaran_upt.m_upt_code')
            ->whereIn('bbm_anggaran_upt.statusperubahan', [0, 2]);

        if ($uptCode) {
            $query->where('bbm_anggaran_upt.m_upt_code', $uptCode);
        }

        $data = $query->orderBy('bbm_anggaran_upt.tanggal_trans', 'desc')->get();

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            $statusText = '';
            if ($item->statusperubahan == 0) {
                $statusText = 'Belum Di Setujui';
            } elseif ($item->statusperubahan == 2) {
                $statusText = 'Pengajuan Dibatalkan';
            } else {
                $statusText = 'Sudah Di Setujui';
            }

            return [
                'UPT' => $item->upt ? $item->upt->nama : '-',
                'Tanggal Transaksi' => $item->tanggal_trans ? \Carbon\Carbon::parse($item->tanggal_trans)->locale('id')->format('d F Y') : '-',
                'Nominal (Rp)' => number_format($item->nominal, 0, ',', '.'),
                'Nomor Surat' => $item->nomor_surat ?: '-',
                'Keterangan' => $item->keterangan ?: '-',
                'Status' => $statusText,
                'User Input' => $item->user_input ?: '-',
                'Tanggal Input' => $item->tanggal_input ? \Carbon\Carbon::parse($item->tanggal_input)->locale('id')->format('d F Y H:i') : '-'
            ];
        });

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Perubahan Anggaran Internal';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    // 6. Laporan Berita Acara Pembayaran Tagihan
    public function beritaAcaraPembayaran()
    {
        return view('laporan.berita-acara-pembayaran');
    }

    public function getBeritaAcaraPembayaranData(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');
        $uptCode = $request->input('upt_code');

        // Query yang sesuai dengan CodeIgniter - menggunakan join dengan bbm_transdetail dan bbm_kapaltrans
        $query = DB::table('bbm_tagihan as d')
            ->leftJoin('bbm_transdetail as a', 'd.no_tagihan', '=', 'a.no_tagihan')
            ->leftJoin('bbm_kapaltrans as b', 'a.nomor_surat', '=', 'b.nomor_surat')
            ->leftJoin('m_kapal as c', 'b.kapal_code', '=', 'c.code_kapal')
            ->select(
                'c.nama_kapal',
                'a.no_invoice',
                'd.tanggal_invoice',
                'b.lokasi_surat',
                'a.volume_isi',
                'a.harga_total',
                DB::raw('a.harga_total * 10 / 100 AS pbbkb'),
                DB::raw('a.harga_total + (a.harga_total * 10 / 100) AS total_harga')
            )
            ->where('d.statustagihan', 1)
            ->where('d.no_tagihan', '!=', '')
            ->where('b.status_ba', 5);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('b.tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('d.m_upt_code', 'like', '%' . $uptCode . '%');
        }

        $data = $query->orderBy('d.no_tagihan')
            ->orderBy('a.no_invoice')
            ->orderBy('d.tanggal_invoice')
            ->get();

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'nama_kapal' => $item->nama_kapal ?: '-',
                'no_invoice' => $item->no_invoice ?: '-',
                'tanggal_invoice' => $item->tanggal_invoice ? \Carbon\Carbon::parse($item->tanggal_invoice)->locale('id')->format('d F Y') : '-',
                'lokasi_surat' => $item->lokasi_surat ?: '-',
                'volume_isi' => $item->volume_isi ?: 0,
                'harga_total' => $item->harga_total ?: 0,
                'pbbkb' => $item->pbbkb ?: 0,
                'total_harga' => $item->total_harga ?: 0
            ];
        });

        if ($request->expectsJson()) {
            return response()->json(['data' => $formattedData]);
        }

        return $formattedData;
    }

    public function exportBeritaAcaraPembayaran(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');
        $uptCode = $request->input('upt_code');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query yang sama dengan getBeritaAcaraPembayaranData
        $query = DB::table('bbm_tagihan as d')
            ->leftJoin('bbm_transdetail as a', 'd.no_tagihan', '=', 'a.no_tagihan')
            ->leftJoin('bbm_kapaltrans as b', 'a.nomor_surat', '=', 'b.nomor_surat')
            ->leftJoin('m_kapal as c', 'b.kapal_code', '=', 'c.code_kapal')
            ->select(
                'c.nama_kapal',
                'a.no_invoice',
                'd.tanggal_invoice',
                'b.lokasi_surat',
                'a.volume_isi',
                'a.harga_total',
                DB::raw('a.harga_total * 10 / 100 AS pbbkb'),
                DB::raw('a.harga_total + (a.harga_total * 10 / 100) AS total_harga')
            )
            ->where('d.statustagihan', 1)
            ->where('d.no_tagihan', '!=', '')
            ->where('b.status_ba', 5);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('b.tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('d.m_upt_code', 'like', '%' . $uptCode . '%');
        }

        $data = $query->orderBy('d.no_tagihan')
            ->orderBy('a.no_invoice')
            ->orderBy('d.tanggal_invoice')
            ->get();

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'No Invoice' => $item->no_invoice ?: '-',
                'Depot' => $item->lokasi_surat ?: '-',
                'Kapal' => $item->nama_kapal ?: '-',
                'Volume (Liter)' => number_format($item->volume_isi ?: 0, 0, ',', '.'),
                'Harga (Rp)' => number_format($item->harga_total ?: 0, 0, ',', '.'),
                'Total (Rp)' => number_format($item->total_harga ?: 0, 0, ',', '.')
            ];
        });

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Berita Acara Pembayaran Tagihan';

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    // 7. Laporan Verifikasi Tagihan
    public function verifikasiTagihan()
    {
        return view('laporan.verifikasi-tagihan');
    }

    public function getVerifikasiTagihanData(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');
        $uptCode = $request->input('upt_code');
        $noTagihan = $request->input('no_tagihan');

        // Query yang sesuai dengan CodeIgniter - menggunakan join dengan bbm_transdetail dan bbm_kapaltrans
        $query = DB::table('bbm_tagihan as d')
            ->leftJoin('bbm_transdetail as a', 'd.no_tagihan', '=', 'a.no_tagihan')
            ->leftJoin('bbm_kapaltrans as b', 'a.nomor_surat', '=', 'b.nomor_surat')
            ->leftJoin('m_kapal as c', 'b.kapal_code', '=', 'c.code_kapal')
            ->leftJoin('m_upt as e', 'd.m_upt_code', '=', 'e.code')
            ->select(
                'b.peruntukan',
                'd.no_spt',
                'c.nama_kapal',
                'e.nama as nama_upt',
                'd.m_upt_code',
                DB::raw("'PATROLI' AS untuk"),
                'a.no_invoice',
                'd.tanggal_invoice',
                'b.lokasi_surat',
                'a.no_so',
                'a.no_do',
                'a.volume_isi',
                'a.harga_total',
                'b.tanggal_sebelum',
                'b.volume_sebelum',
                DB::raw('b.tanggal_sebelum AS tanggal_pemakaian'),
                'b.volume_pemakaian',
                DB::raw('b.tanggal_sebelum AS tanggal_pemeriksaan'),
                'b.volume_sisa',
                DB::raw("IF(b.status_segel=1,'BAIK','RUSAK') AS status_segel"),
                'b.tanggal_surat',
                'a.volume_isi',
                DB::raw('(SELECT COUNT(*) FROM bbm_transdetail WHERE no_tagihan = d.no_tagihan AND no_so = a.no_so GROUP BY no_tagihan,no_so) AS jml_do'),
                'd.no_tagihan'
            )
            ->where('d.statustagihan', 1)
            ->where('d.no_tagihan', '!=', '')
            ->where('b.status_ba', 5);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('b.tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('d.m_upt_code', 'like', '%' . $uptCode . '%');
        }

        if ($noTagihan) {
            $query->whereRaw("REPLACE(d.no_tagihan, '/', '') = ?", [str_replace('/', '', $noTagihan)]);
        }

        $data = $query->orderBy('d.no_tagihan')
            ->orderBy('a.no_invoice')
            ->orderBy('d.tanggal_invoice')
            ->get();

        // Format data untuk frontend
        $formattedData = $data->map(function ($item) {
            return [
                'peruntukan' => $item->peruntukan ?: '-',
                'no_spt' => $item->no_spt ?: '-',
                'nama_kapal' => $item->nama_kapal ?: '-',
                'upt' => [
                    'nama' => $item->nama_upt ?: '-'
                ],
                'm_upt_code' => $item->m_upt_code ?: '-',
                'untuk' => $item->untuk ?: '-',
                'no_invoice' => $item->no_invoice ?: '-',
                'tanggal_invoice' => $item->tanggal_invoice ? \Carbon\Carbon::parse($item->tanggal_invoice)->locale('id')->format('d F Y') : '-',
                'lokasi_surat' => $item->lokasi_surat ?: '-',
                'no_so' => $item->no_so ?: '-',
                'no_do' => $item->no_do ?: '-',
                'volume_isi' => $item->volume_isi ?: 0,
                'harga_total' => $item->harga_total ?: 0,
                'tanggal_sebelum' => $item->tanggal_sebelum ? \Carbon\Carbon::parse($item->tanggal_sebelum)->locale('id')->format('d F Y') : '-',
                'volume_sebelum' => $item->volume_sebelum ?: 0,
                'tanggal_pemakaian' => $item->tanggal_pemakaian ? \Carbon\Carbon::parse($item->tanggal_pemakaian)->locale('id')->format('d F Y') : '-',
                'volume_pemakaian' => $item->volume_pemakaian ?: 0,
                'tanggal_pemeriksaan' => $item->tanggal_pemeriksaan ? \Carbon\Carbon::parse($item->tanggal_pemeriksaan)->locale('id')->format('d F Y') : '-',
                'volume_sisa' => $item->volume_sisa ?: 0,
                'status_segel' => $item->status_segel ?: '-',
                'tanggal_surat' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'jml_do' => $item->jml_do ?: 0,
                'no_tagihan' => $item->no_tagihan ?: '-'
            ];
        });

        if ($request->expectsJson()) {
            return response()->json(['data' => $formattedData]);
        }

        return $formattedData;
    }

    public function exportVerifikasiTagihan(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');
        $uptCode = $request->input('upt_code');
        $noTagihan = $request->input('no_tagihan');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Query yang sama dengan getVerifikasiTagihanData
        $query = DB::table('bbm_tagihan as d')
            ->leftJoin('bbm_transdetail as a', 'd.no_tagihan', '=', 'a.no_tagihan')
            ->leftJoin('bbm_kapaltrans as b', 'a.nomor_surat', '=', 'b.nomor_surat')
            ->leftJoin('m_kapal as c', 'b.kapal_code', '=', 'c.code_kapal')
            ->leftJoin('m_upt as e', 'd.m_upt_code', '=', 'e.code')
            ->select(
                'b.peruntukan',
                'd.no_spt',
                'c.nama_kapal',
                'e.nama as nama_upt',
                'd.m_upt_code',
                DB::raw("'PATROLI' AS untuk"),
                'a.no_invoice',
                'd.tanggal_invoice',
                'b.lokasi_surat',
                'a.no_so',
                'a.no_do',
                'a.volume_isi',
                'a.harga_total',
                'b.tanggal_sebelum',
                'b.volume_sebelum',
                DB::raw('b.tanggal_sebelum AS tanggal_pemakaian'),
                'b.volume_pemakaian',
                DB::raw('b.tanggal_sebelum AS tanggal_pemeriksaan'),
                'b.volume_sisa',
                DB::raw("IF(b.status_segel=1,'BAIK','RUSAK') AS status_segel"),
                'b.tanggal_surat',
                'a.volume_isi',
                DB::raw('(SELECT COUNT(*) FROM bbm_transdetail WHERE no_tagihan = d.no_tagihan AND no_so = a.no_so GROUP BY no_tagihan,no_so) AS jml_do'),
                'd.no_tagihan'
            )
            ->where('d.statustagihan', 1)
            ->where('d.no_tagihan', '!=', '')
            ->where('b.status_ba', 5);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('b.tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('d.m_upt_code', 'like', '%' . $uptCode . '%');
        }

        if ($noTagihan) {
            $query->whereRaw("REPLACE(d.no_tagihan, '/', '') = ?", [str_replace('/', '', $noTagihan)]);
        }

        $data = $query->orderBy('d.no_tagihan')
            ->orderBy('a.no_invoice')
            ->orderBy('d.tanggal_invoice')
            ->get();

        // Format data untuk export
        $formattedData = $data->map(function ($item) {
            return [
                'UPT' => $item->nama_upt ?: '-',
                'Peruntukan' => $item->peruntukan ?: '-',
                'No SPT' => $item->no_spt ?: '-',
                'Nama Kapal' => $item->nama_kapal ?: '-',
                'Untuk' => $item->untuk ?: '-',
                'No Invoice' => $item->no_invoice ?: '-',
                'Tanggal Invoice' => $item->tanggal_invoice ? \Carbon\Carbon::parse($item->tanggal_invoice)->locale('id')->format('d F Y') : '-',
                'Lokasi Surat' => $item->lokasi_surat ?: '-',
                'No SO' => $item->no_so ?: '-',
                'No DO' => $item->no_do ?: '-',
                'Volume Isi (Liter)' => number_format($item->volume_isi ?: 0, 0, ',', '.'),
                'Harga Total (Rp)' => number_format($item->harga_total ?: 0, 0, ',', '.'),
                'Tanggal Sebelum' => $item->tanggal_sebelum ? \Carbon\Carbon::parse($item->tanggal_sebelum)->locale('id')->format('d F Y') : '-',
                'Volume Sebelum (Liter)' => number_format($item->volume_sebelum ?: 0, 0, ',', '.'),
                'Tanggal Pemakaian' => $item->tanggal_pemakaian ? \Carbon\Carbon::parse($item->tanggal_pemakaian)->locale('id')->format('d F Y') : '-',
                'Volume Pemakaian (Liter)' => number_format($item->volume_pemakaian ?: 0, 0, ',', '.'),
                'Tanggal Pemeriksaan' => $item->tanggal_pemeriksaan ? \Carbon\Carbon::parse($item->tanggal_pemeriksaan)->locale('id')->format('d F Y') : '-',
                'Volume Sisa (Liter)' => number_format($item->volume_sisa ?: 0, 0, ',', '.'),
                'Status Segel' => $item->status_segel ?: '-',
                'Tanggal Surat' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->locale('id')->format('d F Y') : '-',
                'Jumlah DO' => $item->jml_do ?: 0,
                'No Tagihan' => $item->no_tagihan ?: '-'
            ];
        });

        $headers = array_keys($formattedData[0] ?? []);
        $title = 'LAP Verifikasi Tagihan';

        // Debug: Log the data structure
        \Log::info('Verifikasi Tagihan Export Data:', [
            'count' => count($formattedData),
            'headers' => $headers,
            'first_row' => $formattedData[0] ?? null,
            'all_keys' => !empty($formattedData) ? array_keys($formattedData[0]) : []
        ]);

        // If no data, return empty response
        if (empty($formattedData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang bisa di export'
            ], 400);
        }

        if ($format === 'pdf') {
            return ExportHelper::exportToPDF($formattedData, $headers, $title);
        } else {
            return ExportHelper::exportToExcel($formattedData, $headers, $title);
        }
    }

    // Export functions
    public function exportExcel(Request $request, $type)
    {
        try {
            $data = $this->getExportData($request, $type);
            $title = $this->getExportTitle($type);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($title);

            // Set headers
            $headers = $this->getExcelHeaders($type);
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Style header
            $headerRange = 'A1:' . chr(ord('A') + count($headers) - 1) . '1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '568FD2']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            // Add data
            $row = 2;
            foreach ($data as $index => $item) {
                $col = 'A';
                $sheet->setCellValue($col . $row, $index + 1); // No
                $col++;

                $rowData = $this->getExcelRowData($item, $type);
                foreach ($rowData as $value) {
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            // Auto size columns
            foreach (range('A', chr(ord('A') + count($headers) - 1)) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Add borders
            $dataRange = 'A1:' . chr(ord('A') + count($headers) - 1) . ($row - 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            $writer = new Xlsx($spreadsheet);
            $filename = $this->getFileName($type, 'xlsx');

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    public function exportPdf(Request $request, $type)
    {
        try {
            $data = $this->getExportData($request, $type);
            $title = $this->getExportTitle($type);
            $filters = $this->getExportFilters($request, $type);

            $pdf = Pdf::loadView('laporan.export.pdf-template', compact('data', 'title', 'filters', 'type'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download($this->getFileName($type, 'pdf'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'riwayat-all');
        $format = $request->input('format', 'excel');

        if ($format === 'pdf') {
            return $this->exportPdf($request, $type);
        } else {
            return $this->exportExcel($request, $type);
        }
    }

    private function getExportData(Request $request, $type)
    {
        switch ($type) {
            case 'anggaran':
                return $this->getAnggaranData($request);
            case 'riwayat-all':
                return $this->getRiwayatAllData($request);
            case 'realisasi-periode':
                return $this->getRealisasiPeriodeData($request);
            case 'transaksi-realisasi-upt':
                return $this->getTransaksiRealisasiUptData($request);
            case 'perubahan-anggaran-internal':
                return $this->getPerubahanAnggaranInternalData($request);
            case 'berita-acara-pembayaran':
                return $this->getBeritaAcaraPembayaranData($request);
            case 'verifikasi-tagihan':
                return $this->getVerifikasiTagihanData($request);
            default:
                return [];
        }
    }

    private function getExportTitle($type)
    {
        $titles = [
            'anggaran' => 'Laporan Anggaran',
            'riwayat-all' => 'Riwayat Anggaran & Realisasi ALL',
            'realisasi-periode' => 'Laporan Realisasi per Periode',
            'transaksi-realisasi-upt' => 'Laporan Transaksi Realisasi UPT',
            'perubahan-anggaran-internal' => 'Laporan Transaksi Perubahan Anggaran Internal UPT',
            'berita-acara-pembayaran' => 'Laporan Berita Acara Pembayaran Tagihan',
            'verifikasi-tagihan' => 'Laporan Verifikasi Tagihan'
        ];

        return $titles[$type] ?? 'Laporan';
    }

    private function getExportFilters(Request $request, $type)
    {
        $filters = [];

        switch ($type) {
            case 'anggaran':
                if ($request->periode) {
                    $filters['Periode'] = $request->periode;
                }
                break;
            case 'riwayat-all':
                if ($request->tgl_awal) {
                    $filters['Tanggal Awal'] = Carbon::parse($request->tgl_awal)->format('d/m/Y');
                }
                if ($request->tgl_akhir) {
                    $filters['Tanggal Akhir'] = Carbon::parse($request->tgl_akhir)->format('d/m/Y');
                }
                break;
            case 'realisasi-periode':
                if ($request->periode) {
                    $filters['Periode'] = $request->periode;
                }
                if ($request->upt_code) {
                    $upt = MUpt::where('code', $request->upt_code)->first();
                    $filters['UPT'] = $upt ? $upt->nama : $request->upt_code;
                }
                break;
            case 'transaksi-realisasi-upt':
                if ($request->tgl_awal) {
                    $filters['Tanggal Awal'] = Carbon::parse($request->tgl_awal)->format('d/m/Y');
                }
                if ($request->tgl_akhir) {
                    $filters['Tanggal Akhir'] = Carbon::parse($request->tgl_akhir)->format('d/m/Y');
                }
                if ($request->upt_code) {
                    $upt = MUpt::where('code', $request->upt_code)->first();
                    $filters['UPT'] = $upt ? $upt->nama : $request->upt_code;
                }
                if ($request->no_tagihan) {
                    $filters['No Tagihan'] = $request->no_tagihan;
                }
                break;
            case 'perubahan-anggaran-internal':
                if ($request->periode) {
                    $filters['Periode'] = $request->periode;
                }
                if ($request->upt_code) {
                    $upt = MUpt::where('code', $request->upt_code)->first();
                    $filters['UPT'] = $upt ? $upt->nama : $request->upt_code;
                }
                break;
            case 'berita-acara-pembayaran':
                if ($request->tgl_awal) {
                    $filters['Tanggal Awal'] = Carbon::parse($request->tgl_awal)->format('d/m/Y');
                }
                if ($request->tgl_akhir) {
                    $filters['Tanggal Akhir'] = Carbon::parse($request->tgl_akhir)->format('d/m/Y');
                }
                if ($request->upt_code) {
                    $upt = MUpt::where('code', $request->upt_code)->first();
                    $filters['UPT'] = $upt ? $upt->nama : $request->upt_code;
                }
                break;
            case 'verifikasi-tagihan':
                if ($request->tgl_awal) {
                    $filters['Tanggal Awal'] = Carbon::parse($request->tgl_awal)->format('d/m/Y');
                }
                if ($request->tgl_akhir) {
                    $filters['Tanggal Akhir'] = Carbon::parse($request->tgl_akhir)->format('d/m/Y');
                }
                if ($request->upt_code) {
                    $upt = MUpt::where('code', $request->upt_code)->first();
                    $filters['UPT'] = $upt ? $upt->nama : $request->upt_code;
                }
                if ($request->no_tagihan) {
                    $filters['No Tagihan'] = $request->no_tagihan;
                }
                break;
        }

        return $filters;
    }

    private function getFileName($type, $extension)
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        return "laporan_{$type}_{$timestamp}.{$extension}";
    }

    private function getExcelHeaders($type)
    {
        switch ($type) {
            case 'anggaran':
                return ['No', 'Periode', 'UPT', 'Total Anggaran (Rp)'];
            case 'riwayat-all':
                return ['No', 'Periode', 'UPT', 'Anggaran (Rp)', 'Total Tagihan (Rp)', 'Sisa Anggaran (Rp)'];
            case 'realisasi-periode':
                return ['No', 'Periode', 'UPT', 'No Tagihan', 'Tanggal Surat', 'Total Realisasi (Rp)'];
            case 'transaksi-realisasi-upt':
                return ['No', 'Periode', 'UPT', 'No Tagihan', 'Tanggal Surat', 'Lokasi Surat', 'Total Realisasi (Rp)'];
            case 'perubahan-anggaran-internal':
                return ['No', 'Periode', 'UPT', 'Tanggal Trans', 'Keterangan', 'Total Anggaran (Rp)'];
            case 'berita-acara-pembayaran':
                return ['No', 'Periode', 'UPT', 'No Tagihan', 'Tanggal Surat', 'Lokasi Surat', 'No Invoice', 'Volume (L)', 'Harga Total (Rp)'];
            case 'verifikasi-tagihan':
                return ['No', 'Periode', 'UPT', 'No Tagihan', 'Tanggal Surat', 'Lokasi Surat', 'No Invoice', 'Volume (L)', 'Harga Total (Rp)', 'Status Segel'];
            default:
                return ['No', 'Data'];
        }
    }

    private function getExcelRowData($item, $type)
    {
        switch ($type) {
            case 'anggaran':
                return [
                    $item->periode,
                    $item->upt ? $item->upt->nama : '-',
                    number_format($item->total_anggaran, 0, ',', '.')
                ];
            case 'riwayat-all':
                return [
                    $item->periode,
                    $item->nama_upt,
                    number_format($item->anggaran, 0, ',', '.'),
                    number_format($item->total_tagihan, 0, ',', '.'),
                    number_format($item->sisa_anggaran, 0, ',', '.')
                ];
            case 'realisasi-periode':
                return [
                    $item->periode,
                    $item->upt ? $item->upt->nama : '-',
                    $item->no_tagihan,
                    Carbon::parse($item->tanggal_surat)->format('d/m/Y'),
                    number_format($item->total_realisasi, 0, ',', '.')
                ];
            case 'transaksi-realisasi-upt':
                return [
                    $item->periode,
                    $item->upt ? $item->upt->nama : '-',
                    $item->no_tagihan,
                    Carbon::parse($item->tanggal_surat)->format('d/m/Y'),
                    $item->lokasi_surat,
                    number_format($item->total_realisasi, 0, ',', '.')
                ];
            case 'perubahan-anggaran-internal':
                return [
                    $item->periode,
                    $item->upt ? $item->upt->nama : '-',
                    Carbon::parse($item->tanggal_trans)->format('d/m/Y'),
                    $item->keterangan,
                    number_format($item->total_anggaran, 0, ',', '.')
                ];
            case 'berita-acara-pembayaran':
                return [
                    $item->periode,
                    $item->upt ? $item->upt->nama : '-',
                    $item->no_tagihan,
                    Carbon::parse($item->tanggal_surat)->format('d/m/Y'),
                    $item->lokasi_surat,
                    $item->no_invoice,
                    number_format($item->volume_isi, 0, ',', '.'),
                    number_format($item->harga_total, 0, ',', '.')
                ];
            case 'verifikasi-tagihan':
                $statusSegel = $item->status_segel == 1 ? 'BAIK' : 'RUSAK';
                return [
                    $item->periode,
                    $item->upt ? $item->upt->nama : '-',
                    $item->no_tagihan,
                    Carbon::parse($item->tanggal_surat)->format('d/m/Y'),
                    $item->lokasi_surat,
                    $item->no_invoice,
                    number_format($item->volume_isi, 0, ',', '.'),
                    number_format($item->harga_total, 0, ',', '.'),
                    $statusSegel
                ];
            default:
                return ['Data'];
        }
    }
}
