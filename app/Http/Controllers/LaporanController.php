<?php

namespace App\Http\Controllers;

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

        $query = BbmTagihan::with(['upt'])
            ->select('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat', DB::raw('SUM(total_harga) as total_tagihan'))
            ->where('statustagihan', 1);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        $data = $query->groupBy('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat')
            ->orderBy('tanggal_surat', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $data]);
        }

        return $data;
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

        $query = BbmTagihan::with(['upt'])
            ->select('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat', DB::raw('SUM(total_harga) as total_realisasi'))
            ->where('statustagihan', 1);

        if ($periode) {
            $query->where('periode', $periode);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        $data = $query->groupBy('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat')
            ->orderBy('tanggal_surat', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $data]);
        }

        return $data;
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

        $query = BbmTagihan::with(['upt', 'transdetail.kapaltrans.kapal'])
            ->select('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat', 'lokasi_surat', DB::raw('SUM(total_harga) as total_realisasi'))
            ->where('statustagihan', 1)
            ->where('status_ba', 5);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        if ($noTagihan) {
            $query->where('no_tagihan', 'like', '%' . $noTagihan . '%');
        }

        $data = $query->groupBy('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat', 'lokasi_surat')
            ->orderBy('tanggal_surat', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $data]);
        }

        return $data;
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

        $query = BbmAnggaranUpt::with(['upt'])
            ->select('periode', 'm_upt_code', 'tanggal_trans', 'keterangan', DB::raw('SUM(anggaran) as total_anggaran'))
            ->whereIn('statusperubahan', [0, 2]);

        if ($periode) {
            $query->where('periode', $periode);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        $data = $query->groupBy('periode', 'm_upt_code', 'tanggal_trans', 'keterangan')
            ->orderBy('tanggal_trans', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $data]);
        }

        return $data;
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

        $query = BbmTagihan::with(['upt', 'transdetail.kapaltrans.kapal'])
            ->select('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat', 'lokasi_surat', 'no_invoice', 'volume_isi', 'harga_total')
            ->where('statustagihan', 1)
            ->where('status_ba', 5);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $data]);
        }

        return $data;
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

        $query = BbmTagihan::with(['upt', 'transdetail.kapaltrans.kapal'])
            ->select('periode', 'm_upt_code', 'no_tagihan', 'tanggal_surat', 'lokasi_surat', 'no_invoice', 'volume_isi', 'harga_total', 'status_segel')
            ->where('statustagihan', 1)
            ->where('status_ba', 5);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir]);
        }

        if ($uptCode) {
            $query->where('m_upt_code', $uptCode);
        }

        if ($noTagihan) {
            $query->where('no_tagihan', 'like', '%' . $noTagihan . '%');
        }

        $data = $query->orderBy('tanggal_surat', 'desc')->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $data]);
        }

        return $data;
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
                return ['No', 'Periode', 'UPT', 'No Tagihan', 'Tanggal Surat', 'Total Tagihan (Rp)'];
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
                    $item->upt ? $item->upt->nama : '-',
                    $item->no_tagihan,
                    Carbon::parse($item->tanggal_surat)->format('d/m/Y'),
                    number_format($item->total_tagihan, 0, ',', '.')
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
