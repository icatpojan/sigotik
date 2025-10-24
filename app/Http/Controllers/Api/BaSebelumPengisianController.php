<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\BbmKapaltrans;
use App\Models\MKapal;
use App\Models\MUpt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BaSebelumPengisianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kapals = MKapal::all();

        return view('ba-sebelum-pengisian.index', compact('kapals'));
    }

    /**
     * Get BA Sebelum Pengisian data via AJAX
     */
    public function getData(Request $request)
    {
        $user = $request->user();
        $query = BbmKapaltrans::with(['kapal', 'fotoUploads'])
            ->where('status_ba', 2); // BA Sebelum Pengisian

        // Role-based filtering
        if ($user->conf_group_id != 1) { // Jika bukan admin
            // Ambil kapal yang terkait dengan user melalui sys_user_kapal
            $userKapalIds = DB::table('sys_user_kapal')
                ->where('conf_user_id', $user->conf_user_id)
                ->pluck('m_kapal_id')
                ->toArray();

            if (!empty($userKapalIds)) {
                // Filter BBM berdasarkan kapal yang dimiliki user
                $query->whereHas('kapal', function ($kapalQuery) use ($userKapalIds) {
                    $kapalQuery->whereIn('m_kapal_id', $userKapalIds);
                });
            } else {
                // Jika user tidak memiliki kapal, return empty
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 10,
                        'total' => 0,
                        'from' => null,
                        'to' => null,
                        'has_more_pages' => false,
                    ]
                ]);
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('kapal_code', 'like', "%{$search}%")
                    ->orWhere('lokasi_surat', 'like', "%{$search}%")
                    ->orWhereHas('kapal', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('nama_kapal', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by kapal
        if ($request->has('kapal') && $request->kapal) {
            $query->where('kapal_code', $request->kapal);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('tanggal_surat', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('tanggal_surat', '<=', $request->date_to);
        }

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $data = $query->orderBy('trans_id', 'desc')->paginate($perPage);

        // Add file URLs to each item
        $items = $data->items();
        foreach ($items as $item) {
            $this->addFileUrls($item);
        }

        return response()->json([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages(),
            ]
        ]);
    }

    /**
     * Get kapal data for auto-fill
     */
    public function getKapalData(Request $request)
    {
        $user = $request->user();
        $kapalId = $request->kapal_id;

        if ($kapalId) {
            // Kalau ada kapal_id → ambil satu kapal
            $query = MKapal::with('upt');

            // Role-based filtering
            if ($user->conf_group_id != 1) { // Jika bukan admin
                // Ambil kapal yang terkait dengan user melalui sys_user_kapal
                $userKapalIds = DB::table('sys_user_kapal')
                    ->where('conf_user_id', $user->conf_user_id)
                    ->pluck('m_kapal_id')
                    ->toArray();

                if (!empty($userKapalIds)) {
                    $query->whereIn('m_kapal_id', $userKapalIds);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kapal tidak ditemukan'
                    ], 404);
                }
            }

            $kapal = $query->find($kapalId);
            if (!$kapal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kapal tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'code_kapal' => $kapal->code_kapal,
                    'alamat_upt' => $kapal->upt ? $kapal->upt->alamat1 : '',
                    'zona_waktu_upt' => $kapal->upt ? $kapal->upt->zona_waktu_upt : 'WIB',
                    'kota' => $kapal->upt ? $kapal->upt->kota : '',
                    'jabatan_petugas' => $kapal->upt ? $kapal->upt->jabatan_petugas : '',
                    'nama_petugas' => $kapal->upt ? $kapal->upt->nama_petugas : '',
                    'nip_petugas' => $kapal->upt ? $kapal->upt->nip_petugas : '',
                    'nama_nakoda' => $kapal->nama_nakoda,
                    'nip_nakoda' => $kapal->nip_nakoda,
                    'nama_kkm' => $kapal->nama_kkm,
                    'nip_kkm' => $kapal->nip_kkm,
                ]
            ]);
        } else {
            // Kalau tidak ada kapal_id → ambil semua kapal
            $kapal = MKapal::with('upt')->get();
            return response()->json([
                'success' => true,
                'data' => $kapal
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code_kapal' => 'required|exists:m_kapal,code_kapal',
            'nomor_surat' => 'required|string|max:50|unique:bbm_kapaltrans,nomor_surat',
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string', // TEXT field, no max limit
            'volume_sisa' => 'required|numeric|min:0',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30', // Sesuai migration
            'nama_staf_pagkalan' => 'nullable|string|max:30', // Sesuai migration
            'nip_staf' => 'nullable|string|max:20', // Sesuai migration
            'nama_nahkoda' => 'nullable|string|max:50', // Sesuai migration
            'nip_nahkoda' => 'nullable|string|max:20', // Sesuai migration
            'nama_kkm' => 'nullable|string|max:30', // Sesuai migration
            'nip_kkm' => 'nullable|string|max:20', // Sesuai migration
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get kapal data
            $kapal = MKapal::find($request->kapal_id) ?? MKapal::where('code_kapal', $request->code_kapal)->first();

            // Get the highest ID and add 1
            $maxId = BbmKapaltrans::max('trans_id') ?? 0;
            $newId = $maxId + 1;

            // Handle file uploads
            $fotoPath = null;
            $ttdPath = null;

            if ($request->hasFile('foto')) {
                $fotoPath = $this->uploadFile($request->file('foto'), 'foto', $newId);
            }

            if ($request->hasFile('ttd')) {
                $ttdPath = $this->uploadFile($request->file('ttd'), 'ttd', $newId);
            }

            $ba = BbmKapaltrans::create([
                'trans_id' => $newId,
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'volume_sisa' => $request->volume_sisa,
                'status_ba' => 2, // BA Sebelum Pengisian
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'), // Cast to integer
                'an_nakhoda' => (int)($request->an_nakhoda == '1'), // Cast to integer
                'an_kkm' => (int)($request->an_kkm == '1'), // Cast to integer
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1), // Cast to string
                'status_trans' => 0, // Input
                'foto' => $fotoPath, // Nama file foto
                'ttd' => $ttdPath, // Nama file tanda tangan
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $ba->trans_id = $newId;
            // $ba->save();

            // Load data dengan URL lengkap
            $ba = $ba->load('kapal');
            $this->addFileUrls($ba);

            return response()->json([
                'success' => true,
                'message' => 'BA Sebelum Pengisian berhasil dibuat',
                'data' => $ba
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = BbmKapaltrans::find($id);

        if ($data) {
            $data = $data->load('kapal');
            $this->addFileUrls($data);
        }

        return response()->json([
            'success' => true,
            'id' => $id,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code_kapal' => 'required|exists:m_kapal,code_kapal',
            // 'nomor_surat' => 'required|string|max:50|unique:bbm_kapaltrans,nomor_surat,' . $id . ',trans_id',
            'tanggal_surat' => 'required|date',
            'jam_surat' => 'required|date_format:H:i',
            'zona_waktu_surat' => 'required|in:WIB,WITA,WIT',
            'lokasi_surat' => 'required|string', // TEXT field, no max limit
            'volume_sisa' => 'required|numeric|min:0',
            'jabatan_staf_pangkalan' => 'nullable|string|max:30', // Sesuai migration
            'nama_staf_pagkalan' => 'nullable|string|max:30', // Sesuai migration
            'nip_staf' => 'nullable|string|max:20', // Sesuai migration
            'nama_nahkoda' => 'nullable|string|max:50', // Sesuai migration
            'nip_nahkoda' => 'nullable|string|max:20', // Sesuai migration
            'nama_kkm' => 'nullable|string|max:30', // Sesuai migration
            'nip_kkm' => 'nullable|string|max:20', // Sesuai migration
            'an_staf' => 'nullable|in:0,1',
            'an_nakhoda' => 'nullable|in:0,1',
            'an_kkm' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get kapal data
            $kapal = MKapal::find($request->kapal_id) ?? MKapal::where('code_kapal', $request->code_kapal)->first();
            $baSebelumPengisian = BbmKapaltrans::find($id);

            // Handle file uploads
            $updateData = [
                'kapal_code' => $kapal->code_kapal,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'jam_surat' => $request->jam_surat,
                'zona_waktu_surat' => $request->zona_waktu_surat,
                'lokasi_surat' => $request->lokasi_surat,
                'volume_sisa' => $request->volume_sisa,
                'jabatan_staf_pangkalan' => $request->jabatan_staf_pangkalan,
                'nama_staf_pagkalan' => $request->nama_staf_pagkalan,
                'nip_staf' => $request->nip_staf,
                'nama_nahkoda' => $request->nama_nahkoda,
                'nip_nahkoda' => $request->nip_nahkoda,
                'nama_kkm' => $request->nama_kkm,
                'nip_kkm' => $request->nip_kkm,
                'an_staf' => (int)($request->an_staf == '1'), // Cast to integer
                'an_nakhoda' => (int)($request->an_nakhoda == '1'), // Cast to integer
                'an_kkm' => (int)($request->an_kkm == '1'), // Cast to integer
                'user_input' => (string)(auth()->user()->conf_user_id ?? 1), // Cast to string
                'updated_at' => now(),
            ];

            // Handle foto upload
            if ($request->hasFile('foto')) {
                // Delete old foto if exists
                if ($baSebelumPengisian->foto && file_exists(public_path('uploads/ba-sebelum-pengisian/' . $baSebelumPengisian->foto))) {
                    unlink(public_path('uploads/ba-sebelum-pengisian/' . $baSebelumPengisian->foto));
                }
                $updateData['foto'] = $this->uploadFile($request->file('foto'), 'foto', $id);
            }

            // Handle ttd upload
            if ($request->hasFile('ttd')) {
                // Delete old ttd if exists
                if ($baSebelumPengisian->ttd && file_exists(public_path('uploads/ba-sebelum-pengisian/' . $baSebelumPengisian->ttd))) {
                    unlink(public_path('uploads/ba-sebelum-pengisian/' . $baSebelumPengisian->ttd));
                }
                $updateData['ttd'] = $this->uploadFile($request->file('ttd'), 'ttd', $id);
            }

            $baSebelumPengisian->update($updateData);

            // Load data dengan URL lengkap
            $baSebelumPengisian = $baSebelumPengisian->load('kapal');
            $this->addFileUrls($baSebelumPengisian);

            return response()->json([
                'success' => true,
                'message' => 'BA Sebelum Pengisian berhasil diperbarui',
                'data' => $baSebelumPengisian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $baSebelumPengisian = BbmKapaltrans::find($id);
            if (!$baSebelumPengisian) {
                return response()->json([
                    'success' => false,
                    'message' => 'BA Sebelum Pengisian tidak ditemukan'
                ], 404);
            }
            $baSebelumPengisian->delete();

            return response()->json([
                'success' => true,
                'message' => 'BA Sebelum Pengisian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for the specified resource.
     */
    public function generatePdf($id)
    {
        try {
            // Load relationship data
            $baSebelumPengisian = BbmKapaltrans::find($id);
            if (!$baSebelumPengisian) {
                return response()->json([
                    'success' => false,
                    'message' => 'BA Sebelum Pengisian tidak ditemukan'
                ], 404);
            }
            $baSebelumPengisian->load(['kapal.upt']);

            // Get data
            $data = $baSebelumPengisian;
            $kapal = $data->kapal;
            $upt = $kapal ? $kapal->upt : null;

            // Format date
            $tanggalFormatted = \Carbon\Carbon::parse($data->tanggal_surat)->locale('id')->isoFormat('dddd, D MMMM YYYY');
            $jamFormatted = str_replace(':', '.', $data->jam_surat);

            // Handle "An." prefix
            $anStaf = ($data->an_staf == 1 || $data->an_staf === true) ? 'An. ' : '';
            $anNakhoda = ($data->an_nakhoda == 1 || $data->an_nakhoda === true) ? 'An. ' : '';
            $anKkm = ($data->an_kkm == 1 || $data->an_kkm === true) ? 'An. ' : '';

            // Create TCPDF instance
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(false);
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
            $pdf->AddPage('P', 'A4');
            $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
            $pdf->SetFont('');

            // Build HTML content
            $html = '<style type="text/css">
                hr.new5 {
                    border: 20px solid green;
                    border-radius: 5px;
                }
            </style>';

            // Header
            $html .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border="0">
                    <tr>
                        <td width="17%" align="center">
                            <img align="center" width="120" height="120" src="' . public_path('images/logo-kkp.png') . '" border="0" />
                        </td>
                        <td width="82%" align="center">
                            <font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
                            <font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
                            <font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
                            <font size="12"><b><i>' . strtoupper($upt ? $upt->nama : 'UPT') . '</b></i></font><br>
                            <font size="10">' . ($upt ? $upt->alamat1 : '') . '</font><br>
                            <font size="10">' . ($upt ? $upt->alamat2 : '') . '</font><br>
                            <font size="10">' . ($upt ? $upt->alamat3 : '') . '</font>
                        </td>
                    </tr>
                </table>';

            // Add lines
            $style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
            $pdf->Line(10, 58, 200, 58, $style);
            $style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
            $pdf->Line(10, 60, 200, 60, $style2);

            // Content
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="center">
                            <font size="12"><b><u>BERITA ACARA SISA BBM SEBELUM PENGISIAN</b></u></font>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" align="center">
                            <b>Nomor : ' . $data->nomor_surat . '</b><br>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" align="justify">Pada hari ini ' . $tanggalFormatted . ' pukul ' . $jamFormatted . ' ' . $data->zona_waktu_surat . ', bertempat di ' . strtoupper($data->lokasi_surat) . ', kami yang bertanda tangan
                            dibawah ini :
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%" align="justify">Nama/Jabatan</td>
                        <td width="2%" align="center">:</td>
                        <td width="3%" align="center">1.</td>
                        <td width="auto" align="justify">' . $data->nama_nahkoda . ' / Nakhoda Kapal Pengawas ' . ($kapal ? $kapal->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td width="20%" align="justify"></td>
                        <td width="2%" align="center">:</td>
                        <td width="3%" align="center">2.</td>
                        <td width="auto" align="justify">' . $data->nama_kkm . ' / KKM Kapal Pengawas ' . ($kapal ? $kapal->nama_kapal : '') . '</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%" align="justify">Alamat</td>
                        <td width="2%" align="center">:</td>
                        <td width="auto" align="justify">' . ($upt ? $upt->alamat1 : '') . '</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100%" align="justify">Menyatakan bahwa telah melakukan pengukuran sisa bbm sebelum pengisian dengan rincian sebagai berikut :
                        </td>
                    </tr>
                </table>';

            // Volume table
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="1">
                    <tr>
                        <td width="40%" align="center">Sisa BBM Sebelum Pengisian</td>
                        <td width="3%" align="center">=</td>
                        <td width="40%" align="center">' . number_format($data->volume_sisa) . '</td>
                        <td width="auto" align="center">Liter</td>
                    </tr>
                </table>';

            // Closing statement
            $html .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border="0">
                    <tr>
                        <td width="100%" align="justify">Demikian Berita Acara Sisa BBM Sebelum Pengisian ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.
                        </td>
                    </tr>
                </table>';

            // Footer signatures
            $html .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border="0">
                    <tr>
                        <td width="40%" align="center">
                            <b>' . $anNakhoda . ' Nakhoda KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_nahkoda . '</u></b><br>
                            <b>NIP. ' . $data->nip_nahkoda . '</b>
                        </td>
                        <td width="20%" align="center"></td>
                        <td width="40%" align="center">
                            <b>' . $anKkm . ' KKM KP. ' . ($kapal ? $kapal->nama_kapal : '') . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_kkm . '</u></b><br>
                            <b>NIP. ' . $data->nip_kkm . '</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" align="center"></td>
                        <td width="40%" align="center">
                            <b><br><br>Menyaksikan:</b><br>
                            <b>' . $anStaf . ' ' . $data->jabatan_staf_pangkalan . '</b><br><br><br><br><br>
                            <b><u>' . $data->nama_staf_pagkalan . '</u></b><br>
                            <b>NIP. ' . $data->nip_staf . '</b>
                        </td>
                        <td width="30%" align="center"></td>
                    </tr>
                </table>';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // OPTIMIZATION: Add images page if there are photos
            $this->addImagesPage($pdf, $data);

            // Generate filename
            $filename = 'BA_Sebelum_Pengisian_' . str_replace('/', '_', $data->nomor_surat) . '_' . date('Y-m-d_H-i-s');

            // Output PDF
            $path = public_path('ba_pdf/' . $filename . '.pdf');
            $pdf->Output($path, 'F');

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil dibuat',
                'filename' => $filename . '.pdf',
                'download_url' => asset('ba_pdf/' . $filename . '.pdf')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload dokumen pendukung
     */
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        try {
            $ba = BbmKapaltrans::where('status_ba', 2)->findOrFail($id);

            if ($request->hasFile('document')) {
                $file = $request->file('document');

                // Generate unique filename
                $filename = 'ba_' . $ba->trans_id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Move file to public/uploads/ba-documents
                $file->move(public_path('uploads/ba-documents'), $filename);

                // Update database
                $ba->update(['file_upload' => $filename]);

                return response()->json([
                    'success' => true,
                    'data' => $ba,
                    'message' => 'Dokumen berhasil diupload',
                    'download_url' => asset('uploads/ba-documents/' . $filename)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View dokumen pendukung
     */
    public function viewDocument($id)
    {
        try {
            $ba = BbmKapaltrans::where('status_ba', 2)->findOrFail($id);

            if (!$ba->file_upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            $filePath = public_path('uploads/ba-documents/' . $ba->file_upload);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File dokumen tidak ditemukan di server'
                ], 404);
            }

            $fileUrl = asset('uploads/ba-documents/' . $ba->file_upload);

            return response()->json([
                'success' => true,
                'file_url' => $fileUrl,
                'filename' => $ba->file_upload
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuka dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete dokumen pendukung
     */
    public function deleteDocument($id)
    {
        try {
            $ba = BbmKapaltrans::where('status_ba', 2)->findOrFail($id);

            if ($ba->file_upload) {
                $filePath = public_path('uploads/ba-documents/' . $ba->file_upload);

                // Delete file from server
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Update database
                $ba->update(['file_upload' => '']);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil dihapus'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload file helper method
     */
    private function uploadFile($file, $type, $transId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $transId . '_' . time() . '.' . $extension;
        $file->move(public_path('uploads/ba-sebelum-pengisian'), $filename);
        return $filename;
    }

    /**
     * Add images page to PDF (OPTIMIZED VERSION)
     */
    private function addImagesPage($pdf, $data)
    {
        // Check if there are any photos to display
        $hasPhotos = $data->foto || $data->ttd || $data->foto_flowmeter || $data->foto_segel;

        if (!$hasPhotos) {
            return; // Skip if no photos
        }

        // Add new page
        $pdf->AddPage('P', 'A4');

        // Header for images page
        $html = '<div style="text-align: center; margin-bottom: 15px;">
            <h2 style="font-size: 14px; font-weight: bold; margin: 0;">LAMPIRAN FOTO</h2>
            <p style="font-size: 10px; margin: 3px 0;">Berita Acara Sisa BBM Sebelum Pengisian</p>
            <p style="font-size: 10px; margin: 3px 0;">Nomor: ' . htmlspecialchars($data->nomor_surat) . '</p>
        </div>';

        // OPTIMIZATION: Pre-cache all image paths
        $imagePaths = $this->preloadImagePaths($data);

        // Main images section - Layout 3 kolom menggunakan table
        $html .= '<div style="margin-bottom: 15px;">';

        // Container untuk foto utama (3 kolom) menggunakan table
        $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>';

        // Foto Flowmeter
        if (!empty($imagePaths['foto_flowmeter'])) {
            $html .= '<td style="width: 33.33%; text-align: center; padding: 8px; vertical-align: top;">
                <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 5px;">Foto Flowmeter</h3>
                <img src="' . $imagePaths['foto_flowmeter'] . '" width="120" height="120">
            </td>';
        }

        // Foto Segel
        if (!empty($imagePaths['foto_segel'])) {
            $html .= '<td style="width: 33.33%; text-align: center; padding: 8px; vertical-align: top;">
                <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 5px;">Foto Segel</h3>
                <img src="' . $imagePaths['foto_segel'] . '" width="120" height="120">
            </td>';
        }

        // Foto Berita Acara
        if (!empty($imagePaths['foto'])) {
            $html .= '<td style="width: 33.33%; text-align: center; padding: 8px; vertical-align: top;">
                <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 5px;">Foto Berita Acara</h3>
                <img src="' . $imagePaths['foto'] . '" width="120" height="120">
            </td>';
        }

        $html .= '</tr></table>'; // Tutup table 3 kolom

        // Tanda Tangan (jika ada)
        if (!empty($imagePaths['ttd'])) {
            $html .= '<div style="margin-bottom: 15px; text-align: center;">
                <h3 style="font-size: 10px; font-weight: bold; margin-bottom: 5px;">Tanda Tangan</h3>
                <img src="' . $imagePaths['ttd'] . '" width="120" height="120">
            </div>';
        }

        $html .= '</div>';

        // Write HTML to PDF
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    /**
     * Preload all image paths to avoid repeated file_exists() calls
     * OPTIMIZATION: Cache all image paths in one go
     */
    private function preloadImagePaths($data)
    {
        $imagePaths = [];

        // Main images (foto_flowmeter, foto_segel, foto, ttd)
        if ($data->foto_flowmeter) {
            $imagePaths['foto_flowmeter'] = $this->getFullImagePath($data->foto_flowmeter, 'foto_flowmeter');
        }

        if ($data->foto_segel) {
            $imagePaths['foto_segel'] = $this->getFullImagePath($data->foto_segel, 'foto_segel');
        }

        if ($data->foto) {
            $imagePaths['foto'] = $this->getFullImagePath($data->foto, 'foto');
        }

        if ($data->ttd) {
            $imagePaths['ttd'] = $this->getFullImagePath($data->ttd, 'ttd');
        }

        return $imagePaths;
    }

    /**
     * Get full image path for PDF (local file system path) - OPTIMIZED
     */
    private function getFullImagePath($filename, $type, $date = null)
    {
        // If filename is null or empty, return empty string
        if (empty($filename)) {
            return '';
        }

        // If already a full URL, extract filename
        if (filter_var($filename, FILTER_VALIDATE_URL)) {
            $filename = basename($filename);
        }

        // Determine the correct path based on type
        switch ($type) {
            case 'foto_flowmeter':
            case 'foto_segel':
            case 'foto':
            case 'ttd':
                // All photos stored in uploads/ba-sebelum-pengisian/
                $fullPath = public_path('uploads/ba-sebelum-pengisian/' . $filename);
                break;

            default:
                $fullPath = public_path('uploads/' . $filename);
        }

        // OPTIMIZATION: Only check file_exists once and cache the result
        static $fileCache = [];
        $cacheKey = $fullPath;

        if (!isset($fileCache[$cacheKey])) {
            $fileCache[$cacheKey] = file_exists($fullPath) ? $fullPath : '';
        }

        return $fileCache[$cacheKey];
    }

    /**
     * Add full URLs to file fields
     */
    private function addFileUrls($data)
    {
        if ($data->foto) {
            $data->foto = url('uploads/ba-sebelum-pengisian/' . $data->foto);
        }
        if ($data->ttd) {
            $data->ttd = url('uploads/ba-sebelum-pengisian/' . $data->ttd);
        }
    }
}
