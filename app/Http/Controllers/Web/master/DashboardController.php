<?php

namespace App\Http\Controllers\Web\master;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Hanya return view tanpa data - data akan dimuat via AJAX
        return view('dashboard');
    }

    public function getStats()
    {
        try {
            $user = auth()->user();
            $uptCode = $user->m_upt_code ?? 0;
            $userGroupId = $user->conf_group_id ?? 0;

            $tglAkhir = Carbon::now()->format('Y-m-d');
            $tahun = Carbon::now()->year;
            $tglAwal = Carbon::create($tahun, 1, 1)->format('Y-m-d');

            $periodeAwal = Carbon::parse($tglAwal)->locale('id')->isoFormat('DD MMMM YYYY');
            $periodeAkhir = Carbon::parse($tglAkhir)->locale('id')->isoFormat('DD MMMM YYYY');

            // Cache key berdasarkan user dan periode
            $cacheKey = "dashboard_stats_{$uptCode}_{$userGroupId}_{$tahun}";

            $stats = cache()->remember($cacheKey, 300, function () use ($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun) {
                return $this->getMainStats($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'periodeAwal' => $periodeAwal,
                    'periodeAkhir' => $periodeAkhir,
                    'tahun' => $tahun
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
            'penerimaan' => $penerimaan,
            'penggunaan' => $penggunaan,
            'anggaran' => $totalAnggaran,
            'realisasi' => $totalRealisasi
        ];
    }

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

    public function getChartData()
    {
        try {
            $user = auth()->user();
            $uptCode = $user->m_upt_code ?? 0;
            $userGroupId = $user->conf_group_id ?? 0;

            $tglAkhir = Carbon::now()->format('Y-m-d');
            $tahun = Carbon::now()->year;
            $tglAwal = Carbon::create($tahun, 1, 1)->format('Y-m-d');

            // Check if data exists for current year, if not use latest available year
            $latestYear = DB::select("SELECT MAX(periode) as max_year FROM bbm_anggaran")[0]->max_year ?? $tahun;
            if ($latestYear != $tahun) {
                $tahun = $latestYear;
                $tglAwal = Carbon::create($tahun, 1, 1)->format('Y-m-d');
                $tglAkhir = Carbon::create($tahun, 12, 31)->format('Y-m-d');
            }

            $chartData = $this->getUptChartData($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun);

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data chart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTableData()
    {
        try {
            $user = auth()->user();
            $uptCode = $user->m_upt_code ?? 0;
            $userGroupId = $user->conf_group_id ?? 0;

            $tglAkhir = Carbon::now()->format('Y-m-d');
            $tahun = Carbon::now()->year;
            $tglAwal = Carbon::create($tahun, 1, 1)->format('Y-m-d');

            // Check if data exists for current year, if not use latest available year
            $latestYear = DB::select("SELECT MAX(periode) as max_year FROM bbm_anggaran")[0]->max_year ?? $tahun;
            if ($latestYear != $tahun) {
                $tahun = $latestYear;
                $tglAwal = Carbon::create($tahun, 1, 1)->format('Y-m-d');
                $tglAkhir = Carbon::create($tahun, 12, 31)->format('Y-m-d');
            }

            $tableData = $this->getUptTableData($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun);

            return response()->json([
                'success' => true,
                'data' => $tableData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data tabel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getUptChartData($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun)
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

    private function getUptTableData($uptCode, $userGroupId, $tglAwal, $tglAkhir, $tahun)
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

        $tableData = [];

        foreach ($results as $row) {
            $anggaran = (float) $row->anggaran;
            $realisasi = (float) $row->tagihan;
            $sisaAnggaran = $anggaran - $realisasi;
            $persentase = $anggaran > 0 ? ($realisasi / $anggaran) * 100 : 0;

            $tableData[] = [
                'nama' => $row->nama,
                'anggaran' => $anggaran,
                'realisasi' => $realisasi,
                'sisa_anggaran' => $sisaAnggaran,
                'persentase' => $persentase
            ];
        }

        return $tableData;
    }
}
