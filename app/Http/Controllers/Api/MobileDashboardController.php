<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;
use App\Models\BbmAnggaran;
use App\Models\BbmTagihan;

class MobileDashboardController extends Controller
{
    /**
     * Get comprehensive dashboard data for mobile
     */
    public function getDashboardData(Request $request)
    {
        try {
            $user = $request->user();
            $uptCode = $user->m_upt_code ?? 0;
            $userGroupId = $user->conf_group_id ?? 0;

            $tglAkhir = Carbon::now()->format('Y-m-d');
            $tahun = Carbon::now()->year;
            $tglAwal = Carbon::create($tahun, 1, 1)->format('Y-m-d');

            // Get main statistics
            $stats = $this->getMainStats($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun);

            // Get chart data
            $chartData = $this->getChartData($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun);

            // Get recent transactions
            $recentTransactions = $this->getRecentTransactions($uptCode, $userGroupId);

            // Get pending approvals
            $pendingApprovals = $this->getPendingApprovals($uptCode, $userGroupId);

            // Get UPT performance data
            $uptPerformance = $this->getUptPerformance($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun);

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'chart_data' => $chartData,
                    'recent_transactions' => $recentTransactions,
                    'pending_approvals' => $pendingApprovals,
                    'upt_performance' => $uptPerformance,
                    'periode' => [
                        'awal' => $tglAwal,
                        'akhir' => $tglAkhir,
                        'tahun' => $tahun
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get main statistics
     */
    private function getMainStats($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun)
    {
        // Query untuk penerimaan dan penggunaan BBM
        $bbmQuery = $this->buildBbmQuery($uptCode, $userGroupId, $tglAwal, $tglAkhir);
        $bbmStats = DB::select($bbmQuery);

        $penerimaan = $bbmStats[0]->penerimaan ?? 0;
        $penggunaan = $bbmStats[0]->penggunaan ?? 0;

        // Query untuk anggaran dan realisasi
        $anggaranQuery = $this->buildAnggaranQuery($uptCode, $tglAwal, $tglAkhir, $tahun);
        $anggaranStats = DB::select($anggaranQuery);

        $totalAnggaran = $anggaranStats[0]->anggaran ?? 0;
        $totalRealisasi = $anggaranStats[0]->tagihan ?? 0;

        return [
            'penerimaan_bbm' => (float) $penerimaan,
            'penggunaan_bbm' => (float) $penggunaan,
            'anggaran' => (float) $totalAnggaran,
            'realisasi' => (float) $totalRealisasi,
            'sisa_anggaran' => (float) ($totalAnggaran - $totalRealisasi),
            'persentase_realisasi' => $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 2) : 0
        ];
    }

    /**
     * Get chart data for mobile
     */
    private function getChartData($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun)
    {
        $uptCondition = $uptCode == 0 ? "" : "AND u.code = '{$uptCode}'";

        $query = "
            SELECT u.code, u.nama, a.anggaran,
                COALESCE((
                    SELECT SUM(bt.total)
                    FROM bbm_tagihan bt
                    WHERE bt.m_upt_code = u.code
                    AND bt.statustagihan = 1
                    AND bt.tanggal_invoice BETWEEN '{$tglAwal}' AND '{$tglAkhir}'
                ), 0) AS tagihan
            FROM m_upt u
            JOIN bbm_anggaran a ON u.code = a.m_upt_code
            WHERE 1=1 {$uptCondition} AND a.statusanggaran = '1'
            AND a.periode = '{$tahun}' AND a.perubahan_ke = (
                SELECT MAX(perubahan_ke) FROM bbm_anggaran a2
                WHERE a2.m_upt_code = a.m_upt_code AND a2.periode = '{$tahun}'
            )
            ORDER BY u.m_upt_id
        ";

        $results = DB::select($query);

        $labels = [];
        $anggaranData = [];
        $realisasiData = [];

        foreach ($results as $row) {
            $labels[] = $row->nama;
            $anggaranData[] = (float) $row->anggaran;
            $realisasiData[] = (float) $row->tagihan;
        }

        return [
            'labels' => $labels,
            'anggaran' => $anggaranData,
            'realisasi' => $realisasiData
        ];
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions($uptCode, $userGroupId)
    {
        try {
            // Build where condition based on user role
            $whereCondition = "";

            if ($userGroupId != 1 && $userGroupId != 5) {
                if ($userGroupId == 2) {
                    $whereCondition = "WHERE k.m_upt_code = '{$uptCode}'";
                } else {
                    $whereCondition = "WHERE EXISTS (
                        SELECT 1 FROM sys_user_kapal suk
                        WHERE suk.m_kapal_id = k.m_kapal_id
                        AND suk.conf_user_id = " . auth()->id() . "
                    )";
                }
            }

            $query = "
                SELECT b.trans_id, b.nomor_surat, b.tanggal_surat, b.jam_surat, b.zona_waktu_surat,
                       b.lokasi_surat, b.kapal_code, b.status_ba, b.status_trans,
                       b.volume_pengisian, b.volume_pemakaian, b.penyedia,
                       k.nama_kapal, u.nama_lengkap as user_input_name,
                       b.created_at, b.updated_at
                FROM bbm_kapaltrans b
                LEFT JOIN m_kapal k ON b.kapal_code = k.code_kapal
                LEFT JOIN conf_user u ON b.user_input = u.conf_user_id
                {$whereCondition}
                ORDER BY b.tanggal_surat DESC, b.jam_surat DESC
                LIMIT 10
            ";

            $results = DB::select($query);

            return collect($results)->map(function ($row) {
                return [
                    'id' => $row->trans_id,
                    'nomor_surat' => $row->nomor_surat,
                    'tanggal_surat' => $row->tanggal_surat,
                    'jam_surat' => $row->jam_surat,
                    'zona_waktu' => $row->zona_waktu_surat,
                    'lokasi_surat' => $row->lokasi_surat,
                    'kapal_nama' => $row->nama_kapal ?: $row->kapal_code,
                    'status_ba' => $row->status_ba,
                    'status_ba_text' => $this->getStatusBaText($row->status_ba),
                    'status_trans' => $row->status_trans,
                    'status_trans_text' => $this->getStatusTransText($row->status_trans),
                    'volume_pengisian' => $row->volume_pengisian,
                    'volume_pemakaian' => $row->volume_pemakaian,
                    'penyedia' => $row->penyedia,
                    'user_input' => $row->user_input_name,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getRecentTransactions: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get pending approvals
     */
    private function getPendingApprovals($uptCode, $userGroupId)
    {
        try {
            // Build where condition based on user role
            $whereCondition = "WHERE b.status_trans = 0";

            if ($userGroupId != 1 && $userGroupId != 5) {
                if ($userGroupId == 2) {
                    $whereCondition .= " AND k.m_upt_code = '{$uptCode}'";
                } else {
                    $whereCondition .= " AND EXISTS (
                        SELECT 1 FROM sys_user_kapal suk
                        WHERE suk.m_kapal_id = k.m_kapal_id
                        AND suk.conf_user_id = " . auth()->id() . "
                    )";
                }
            }

            $query = "
                SELECT b.trans_id, b.nomor_surat, b.tanggal_surat, b.kapal_code,
                       b.volume_pengisian, b.penyedia, b.status_ba,
                       k.nama_kapal, u.nama_lengkap as user_input_name,
                       b.created_at
                FROM bbm_kapaltrans b
                LEFT JOIN m_kapal k ON b.kapal_code = k.code_kapal
                LEFT JOIN conf_user u ON b.user_input = u.conf_user_id
                {$whereCondition}
                ORDER BY b.tanggal_surat DESC
                LIMIT 10
            ";

            $results = DB::select($query);

            return collect($results)->map(function ($row) {
                return [
                    'id' => $row->trans_id,
                    'nomor_surat' => $row->nomor_surat,
                    'tanggal_surat' => $row->tanggal_surat,
                    'kapal_nama' => $row->nama_kapal ?: $row->kapal_code,
                    'status_ba_text' => $this->getStatusBaText($row->status_ba),
                    'volume_pengisian' => $row->volume_pengisian,
                    'penyedia' => $row->penyedia,
                    'user_input' => $row->user_input_name,
                    'created_at' => $row->created_at
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getPendingApprovals: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get UPT performance data
     */
    private function getUptPerformance($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun)
    {
        $uptCondition = $uptCode == 0 ? "" : "AND u.code = '{$uptCode}'";

        $query = "
            SELECT u.code, u.nama, a.anggaran,
                COALESCE((
                    SELECT SUM(bt.total)
                    FROM bbm_tagihan bt
                    WHERE bt.m_upt_code = u.code
                    AND bt.statustagihan = 1
                    AND bt.tanggal_invoice BETWEEN '{$tglAwal}' AND '{$tglAkhir}'
                ), 0) AS tagihan
            FROM m_upt u
            JOIN bbm_anggaran a ON u.code = a.m_upt_code
            WHERE 1=1 {$uptCondition} AND a.statusanggaran = '1'
            AND a.periode = '{$tahun}' AND a.perubahan_ke = (
                SELECT MAX(perubahan_ke) FROM bbm_anggaran a2
                WHERE a2.m_upt_code = a.m_upt_code AND a2.periode = '{$tahun}'
            )
            ORDER BY u.m_upt_id
        ";

        $results = DB::select($query);

        return collect($results)->map(function ($row) {
            $anggaran = (float) $row->anggaran;
            $realisasi = (float) $row->tagihan;
            $sisaAnggaran = $anggaran - $realisasi;
            $persentase = $anggaran > 0 ? ($realisasi / $anggaran) * 100 : 0;

            return [
                'upt_code' => $row->code,
                'upt_nama' => $row->nama,
                'anggaran' => $anggaran,
                'realisasi' => $realisasi,
                'sisa_anggaran' => $sisaAnggaran,
                'persentase_realisasi' => round($persentase, 2)
            ];
        });
    }

    /**
     * Build BBM query for statistics
     */
    private function buildBbmQuery($uptCode, $userGroupId, $tglAwal, $tglAkhir)
    {
        $whereClause1 = $this->getWhereClause($uptCode, $userGroupId);
        $whereClause2 = $this->getWhereClauseForSecondQuery($uptCode, $userGroupId);

        return "
            SELECT IFNULL(SUM(penerimaan),0) AS penerimaan, IFNULL(SUM(penggunaan),0) AS penggunaan
            FROM (
                SELECT kapal_code, SUM(volume_isi) AS penerimaan, 0 AS penggunaan
                FROM bbm_kapaltrans a
                JOIN bbm_transdetail b ON a.nomor_surat = b.nomor_surat
                JOIN m_kapal c ON TRIM(c.code_kapal) = TRIM(a.kapal_code)
                WHERE a.status_upload = 1 AND a.status_ba = '5'
                AND a.tanggal_surat >= '{$tglAwal}' AND a.tanggal_surat <= '{$tglAkhir}'
                {$whereClause1}
                GROUP BY a.kapal_code
                UNION
                SELECT kapal_code, 0 AS penerimaan, SUM(volume_pemakaian) AS penggunaan
                FROM bbm_kapaltrans a
                JOIN m_kapal b ON a.kapal_code = b.code_kapal
                WHERE a.status_upload = 1 AND a.status_ba = '3'
                AND a.tanggal_surat >= '{$tglAwal}' AND a.tanggal_surat <= '{$tglAkhir}'
                {$whereClause2}
                GROUP BY a.kapal_code
            ) trans
        ";
    }

    /**
     * Build anggaran query for statistics
     */
    private function buildAnggaranQuery($uptCode, $tglAwal, $tglAkhir, $tahun)
    {
        $uptCondition = $uptCode == 0 ? "" : "AND m_upt_code = '{$uptCode}'";

        return "
            SELECT IFNULL(SUM(anggaran),0) AS anggaran, IFNULL(SUM(tagihan),0) AS tagihan
            FROM (
                SELECT m_upt.code, m_upt.nama, anggaran,
                    (
                        SELECT SUM(tagihan) FROM (
                            SELECT STR_TO_DATE(tanggal_input,'%Y-%m-%d') AS tanggal_input, 0 AS tagihan
                            FROM bbm_anggaran
                            WHERE 1=1 {$uptCondition} AND periode = '{$tahun}' AND perubahan_ke = (
                                SELECT MAX(perubahan_ke) FROM bbm_anggaran
                                WHERE m_upt_code = bbm_anggaran.m_upt_code AND periode = '{$tahun}'
                            )
                            UNION ALL
                            SELECT tanggal_invoice AS tanggal_input, total AS tagihan
                            FROM bbm_tagihan
                            WHERE 1=1 {$uptCondition} AND statustagihan = 1
                            AND tanggal_invoice BETWEEN '{$tglAwal}' AND '{$tglAkhir}'
                        ) as tagihan
                    ) AS tagihan
                FROM bbm_anggaran
                JOIN m_upt ON m_upt.code = bbm_anggaran.m_upt_code
                WHERE 1=1 {$uptCondition} AND statusanggaran = '1'
                AND periode = '{$tahun}' AND perubahan_ke = (
                    SELECT MAX(perubahan_ke) FROM bbm_anggaran
                    WHERE m_upt_code = bbm_anggaran.m_upt_code AND periode = '{$tahun}'
                )
            ) jmlx
        ";
    }

    /**
     * Get where clause for BBM query
     */
    private function getWhereClause($uptCode, $userGroupId)
    {
        if ($userGroupId == 1 || $userGroupId == 5) {
            return "";
        } elseif ($userGroupId == 2) {
            return "AND c.m_upt_code = '{$uptCode}'";
        } else {
            return "AND a.kapal_code IN (SELECT f.code_kapal FROM sys_user_kapal b JOIN m_kapal f ON b.m_kapal_id = f.m_kapal_id WHERE b.conf_user_id = '" . auth()->id() . "')";
        }
    }

    /**
     * Get where clause for second BBM query
     */
    private function getWhereClauseForSecondQuery($uptCode, $userGroupId)
    {
        if ($userGroupId == 1 || $userGroupId == 5) {
            return "";
        } elseif ($userGroupId == 2) {
            return "AND b.m_upt_code = '{$uptCode}'";
        } else {
            return "AND a.kapal_code IN (SELECT f.code_kapal FROM sys_user_kapal b JOIN m_kapal f ON b.m_kapal_id = f.m_kapal_id WHERE b.conf_user_id = '" . auth()->id() . "')";
        }
    }

    /**
     * Get quick stats for mobile
     */
    public function getQuickStats(Request $request)
    {
        try {
            $user = $request->user();
            $uptCode = $user->m_upt_code ?? 0;
            $userGroupId = $user->conf_group_id ?? 0;

            $stats = [
                'total_transactions' => $this->getTotalTransactions($uptCode, $userGroupId),
                'pending_approvals' => $this->getPendingCount($uptCode, $userGroupId),
                'total_kapals' => $this->getTotalKapals($uptCode, $userGroupId),
                'total_upts' => $this->getTotalUpts($uptCode, $userGroupId)
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat quick stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getTotalTransactions($uptCode, $userGroupId)
    {
        $query = BbmKapaltrans::query();

        if ($userGroupId != 1 && $userGroupId != 5) {
            if ($userGroupId == 2) {
                $query->whereHas('kapal', function ($q) use ($uptCode) {
                    $q->where('m_upt_code', $uptCode);
                });
            } else {
                $query->whereHas('kapal.users', function ($q) {
                    $q->where('sys_user_kapal.conf_user_id', auth()->id());
                });
            }
        }

        return $query->count();
    }

    private function getPendingCount($uptCode, $userGroupId)
    {
        $query = BbmKapaltrans::where('status_trans', 0);

        if ($userGroupId != 1 && $userGroupId != 5) {
            if ($userGroupId == 2) {
                $query->whereHas('kapal', function ($q) use ($uptCode) {
                    $q->where('m_upt_code', $uptCode);
                });
            } else {
                $query->whereHas('kapal.users', function ($q) {
                    $q->where('sys_user_kapal.conf_user_id', auth()->id());
                });
            }
        }

        return $query->count();
    }

    private function getTotalKapals($uptCode, $userGroupId)
    {
        $query = MKapal::query();

        if ($userGroupId == 2) {
            $query->where('m_upt_code', $uptCode);
        } elseif ($userGroupId != 1 && $userGroupId != 5) {
            $query->whereHas('users', function ($q) {
                $q->where('conf_user_id', auth()->id());
            });
        }

        return $query->count();
    }

    private function getTotalUpts($uptCode, $userGroupId)
    {
        if ($userGroupId == 1 || $userGroupId == 5) {
            return MUpt::count();
        } else {
            return 1; // User hanya bisa akses UPT sendiri
        }
    }

    /**
     * Get status BA text
     */
    private function getStatusBaText($statusBa)
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
            12 => 'BA Pemberi Hibah BBM Kapal Pengawas',
            13 => 'BA Penerima Hibah BBM Kapal Pengawas',
            14 => 'BA Penerima Hibah BBM Instansi Lain',
            15 => 'BA Akhir Bulan'
        ];

        return $statusMap[$statusBa] ?? 'Unknown';
    }

    /**
     * Get status trans text
     */
    private function getStatusTransText($statusTrans)
    {
        $statusMap = [
            0 => 'Input',
            1 => 'Approval',
            2 => 'Batal'
        ];

        return $statusMap[$statusTrans] ?? 'Unknown';
    }
}
