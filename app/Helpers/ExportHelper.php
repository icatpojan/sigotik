<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Carbon\Carbon;

class ExportHelper
{
    /**
     * Export data to Excel
     */
    public static function exportToExcel($data, $headers, $title, $filename = null)
    {
        // Validasi data kosong
        if (empty($data) || count($data) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang bisa di export'
            ], 400);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:' . chr(65 + count($headers) - 1) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('568FD2');
            $sheet->getStyle($col . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // Set data
        $row = 4;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($headers as $header) {
                $value = $item[$header] ?? '';
                $sheet->setCellValue($col . $row, $value);
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $row++;
        }

        // Auto size columns
        foreach (range('A', chr(65 + count($headers) - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A3:' . chr(65 + count($headers) - 1) . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Generate filename
        if (!$filename) {
            $filename = $title . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export data to PDF
     */
    public static function exportToPDF($data, $headers, $title, $filename = null)
    {
        // Validasi data kosong
        if (empty($data) || count($data) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang bisa di export'
            ], 400);
        }
        // Generate filename
        if (!$filename) {
            $filename = $title . '_' . date('Y-m-d_H-i-s') . '.pdf';
        }

        $pdf = Pdf::loadView('laporan.export.pdf-template', [
            'data' => $data,
            'headers' => $headers,
            'title' => $title,
            'exportDate' => Carbon::now()->format('d F Y H:i:s')
        ]);

        return $pdf->download($filename);
    }

    /**
     * Format data for export
     */
    public static function formatDataForExport($data, $type = 'bbm')
    {
        $formattedData = [];


        foreach ($data as $index => $item) {
            // Base row structure
            $row = [];

            // Add specific columns based on type
            switch ($type) {
                case 'total-penerimaan-penggunaan':
                    $row = [
                        'No' => $index + 1,
                        'Nama Kapal' => $item->nama_kapal ?? '-',
                        'Total Penerimaan' => number_format($item->total_penerimaan ?? 0, 0, ',', '.') . ' Liter',
                        'Total Penggunaan' => number_format($item->total_penggunaan ?? 0, 0, ',', '.') . ' Liter',
                    ];
                    break;

                default:
                    $row = [
                        'No' => $index + 1,
                        'Tanggal Transaksi' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->format('d F Y') : '-',
                        'UPT' => $item->kapal && $item->kapal->upt ? $item->kapal->upt->nama : '-',
                        'Kapal' => $item->kapal ? $item->kapal->nama_kapal : '-',
                        'Nomor Surat' => $item->nomor_surat ?? '-',
                        'Status BA' => self::getStatusBaText($item->status_ba ?? 0),
                    ];

                case 'detail-penggunaan-penerimaan':
                    $row = [
                        'No' => $index + 1,
                        'Tanggal BA' => $item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->format('d F Y') : '-',
                        'Nomor BA' => $item->nomor_surat ?? '-',
                        'Nama Kapal' => $item->nama_kapal ?? '-',
                        'Total Penerimaan' => number_format($item->penerimaan ?? 0, 0, ',', '.') . ' Liter',
                        'Total Penggunaan' => number_format($item->penggunaan ?? 0, 0, ',', '.') . ' Liter',
                    ];
                    break;

                case 'akhir-bulan':
                    $row['Volume Sisa'] = ($item->volume_sisa ?? 0) . ' Liter';
                    $row['Volume Sebelum'] = ($item->volume_sebelum ?? 0) . ' Liter';
                    break;

                case 'penerimaan':
                    $row['Volume Pengisian'] = ($item->volume_pengisian ?? 0) . ' Liter';
                    $row['Penyedia'] = $item->penyedia ?? '-';
                    break;

                case 'penitipan':
                    $row['Volume Penitipan'] = ($item->penggunaan ?? 0) . ' Liter';
                    $row['Nama Penitip'] = $item->nama_penitip ?? '-';
                    break;

                case 'pengembalian':
                    $row['Volume Pengembalian'] = ($item->volume_pemakaian ?? 0) . ' Liter';
                    $row['Nama Penitip'] = $item->nama_penitip ?? '-';
                    break;

                case 'peminjaman':
                    $row['Volume Peminjaman'] = ($item->penggunaan ?? 0) . ' Liter';
                    $row['Alasan'] = $item->sebab_temp ?? '-';
                    break;

                case 'pengembalian-pinjaman':
                    $row['Volume Pengembalian'] = ($item->volume_pemakaian ?? 0) . ' Liter';
                    $row['Alasan'] = $item->sebab_temp ?? '-';
                    break;

                case 'pinjaman-belum-dikembalikan':
                    $row['Volume Peminjaman'] = ($item->penggunaan ?? 0) . ' Liter';
                    $row['Alasan'] = $item->sebab_temp ?? '-';
                    $row['Tanggal Peminjaman'] = $item->tanggal_surat ?? '-';
                    break;

                case 'hibah-antar-kapal-pengawas':
                    $row['Volume Hibah'] = ($item->penggunaan ?? 0) . ' Liter';
                    $row['Instansi Pemberi'] = $item->instansi_temp ?? '-';
                    break;

                case 'pemberi-hibah-instansi-lain':
                    $row['Volume Hibah'] = ($item->penggunaan ?? 0) . ' Liter';
                    $row['Instansi Pemberi'] = $item->instansi_temp ?? '-';
                    break;

                case 'penerima-hibah-instansi-lain':
                    $row['Volume Hibah'] = ($item->penggunaan ?? 0) . ' Liter';
                    $row['Instansi Pemberi'] = $item->instansi_temp ?? '-';
                    break;

                case 'penerimaan-hibah':
                    $row['Volume Hibah'] = ($item->penggunaan ?? 0) . ' Liter';
                    $row['Instansi Pemberi'] = $item->instansi_temp ?? '-';
                    break;
            }

            $formattedData[] = $row;
        }


        return $formattedData;
    }

    /**
     * Get status BA text
     */
    private static function getStatusBaText($status)
    {
        $statusMap = [
            0 => 'BA Default',
            1 => 'BA Akhir Bulan',
            2 => 'BA Sebelum Pengisian',
            3 => 'BA Penggunaan BBM',
            4 => 'BA Pemeriksaan Sarana Pengisian',
            5 => 'BA Penerimaan BBM',
            6 => 'BA Sebelum Pelayaran',
            7 => 'BA Sesudah Pelayaran',
            8 => 'BA Penitipan BBM',
            9 => 'BA Pengembalian BBM',
            10 => 'BA Peminjaman BBM',
            11 => 'BA Penerimaan Pinjaman BBM',
            12 => 'BA Pengembalian Pinjaman BBM',
            13 => 'BA Pemberi Hibah BBM Kapal Pengawas',
            14 => 'BA Penerima Hibah BBM Kapal Pengawas',
            15 => 'BA Penerima Hibah BBM Instansi Lain',
            16 => 'BA Pemberi Hibah BBM Instansi Lain',
            17 => 'BA Penerimaan Hibah BBM',
            18 => 'BA Penerima Hibah BBM Instansi Lain'
        ];

        return $statusMap[$status] ?? 'Unknown';
    }
}
