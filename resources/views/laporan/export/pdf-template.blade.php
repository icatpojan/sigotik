<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #568fd2;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #568fd2;
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }

        .filters {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .filters h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
        }

        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }

        .filter-label {
            font-weight: bold;
            color: #555;
        }

        .filter-value {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #568fd2;
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .status-baik {
            color: #28a745;
            font-weight: bold;
        }

        .status-rusak {
            color: #dc3545;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h3>Filter yang Digunakan:</h3>
        @foreach($filters as $label => $value)
        <div class="filter-item">
            <span class="filter-label">{{ $label }}:</span>
            <span class="filter-value">{{ $value }}</span>
        </div>
        @endforeach
    </div>
    @endif

    <table>
        <thead>
            <tr>
                @switch($type)
                @case('anggaran')
                <th class="text-center">No</th>
                <th class="text-center">Periode</th>
                <th class="text-center">UPT</th>
                <th class="text-right">Total Anggaran (Rp)</th>
                @break
                @case('riwayat-all')
                <th class="text-center">No</th>
                <th class="text-center">Periode</th>
                <th class="text-center">UPT</th>
                <th class="text-center">No Tagihan</th>
                <th class="text-center">Tanggal Surat</th>
                <th class="text-right">Total Tagihan (Rp)</th>
                @break
                @case('realisasi-periode')
                <th class="text-center">No</th>
                <th class="text-center">Periode</th>
                <th class="text-center">UPT</th>
                <th class="text-center">No Tagihan</th>
                <th class="text-center">Tanggal Surat</th>
                <th class="text-right">Total Realisasi (Rp)</th>
                @break
                @case('transaksi-realisasi-upt')
                <th class="text-center">No</th>
                <th class="text-center">Periode</th>
                <th class="text-center">UPT</th>
                <th class="text-center">No Tagihan</th>
                <th class="text-center">Tanggal Surat</th>
                <th class="text-center">Lokasi Surat</th>
                <th class="text-right">Total Realisasi (Rp)</th>
                @break
                @case('perubahan-anggaran-internal')
                <th class="text-center">No</th>
                <th class="text-center">Periode</th>
                <th class="text-center">UPT</th>
                <th class="text-center">Tanggal Trans</th>
                <th class="text-center">Keterangan</th>
                <th class="text-right">Total Anggaran (Rp)</th>
                @break
                @case('berita-acara-pembayaran')
                <th class="text-center">No</th>
                <th class="text-center">Periode</th>
                <th class="text-center">UPT</th>
                <th class="text-center">No Tagihan</th>
                <th class="text-center">Tanggal Surat</th>
                <th class="text-center">Lokasi Surat</th>
                <th class="text-center">No Invoice</th>
                <th class="text-right">Volume (L)</th>
                <th class="text-right">Harga Total (Rp)</th>
                @break
                @case('verifikasi-tagihan')
                <th class="text-center">No</th>
                <th class="text-center">Periode</th>
                <th class="text-center">UPT</th>
                <th class="text-center">No Tagihan</th>
                <th class="text-center">Tanggal Surat</th>
                <th class="text-center">Lokasi Surat</th>
                <th class="text-center">No Invoice</th>
                <th class="text-right">Volume (L)</th>
                <th class="text-right">Harga Total (Rp)</th>
                <th class="text-center">Status Segel</th>
                @break
                @endswitch
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>

                @switch($type)
                @case('anggaran')
                <td class="text-center">{{ $row->periode }}</td>
                <td>{{ $row->upt ? $row->upt->nama : '-' }}</td>
                <td class="text-right">{{ number_format($row->total_anggaran, 0, ',', '.') }}</td>
                @break
                @case('riwayat-all')
                <td class="text-center">{{ $row->periode }}</td>
                <td>{{ $row->upt ? $row->upt->nama : '-' }}</td>
                <td class="text-center">{{ $row->no_tagihan }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                @break
                @case('realisasi-periode')
                <td class="text-center">{{ $row->periode }}</td>
                <td>{{ $row->upt ? $row->upt->nama : '-' }}</td>
                <td class="text-center">{{ $row->no_tagihan }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($row->total_realisasi, 0, ',', '.') }}</td>
                @break
                @case('transaksi-realisasi-upt')
                <td class="text-center">{{ $row->periode }}</td>
                <td>{{ $row->upt ? $row->upt->nama : '-' }}</td>
                <td class="text-center">{{ $row->no_tagihan }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y') }}</td>
                <td>{{ $row->lokasi_surat }}</td>
                <td class="text-right">{{ number_format($row->total_realisasi, 0, ',', '.') }}</td>
                @break
                @case('perubahan-anggaran-internal')
                <td class="text-center">{{ $row->periode }}</td>
                <td>{{ $row->upt ? $row->upt->nama : '-' }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_trans)->format('d/m/Y') }}</td>
                <td>{{ $row->keterangan }}</td>
                <td class="text-right">{{ number_format($row->total_anggaran, 0, ',', '.') }}</td>
                @break
                @case('berita-acara-pembayaran')
                <td class="text-center">{{ $row->periode }}</td>
                <td>{{ $row->upt ? $row->upt->nama : '-' }}</td>
                <td class="text-center">{{ $row->no_tagihan }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y') }}</td>
                <td>{{ $row->lokasi_surat }}</td>
                <td class="text-center">{{ $row->no_invoice }}</td>
                <td class="text-right">{{ number_format($row->volume_isi, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->harga_total, 0, ',', '.') }}</td>
                @break
                @case('verifikasi-tagihan')
                <td class="text-center">{{ $row->periode }}</td>
                <td>{{ $row->upt ? $row->upt->nama : '-' }}</td>
                <td class="text-center">{{ $row->no_tagihan }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y') }}</td>
                <td>{{ $row->lokasi_surat }}</td>
                <td class="text-center">{{ $row->no_invoice }}</td>
                <td class="text-right">{{ number_format($row->volume_isi, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->harga_total, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($row->status_segel == 1)
                    <span class="status-baik">BAIK</span>
                    @else
                    <span class="status-rusak">RUSAK</span>
                    @endif
                </td>
                @break
                @endswitch
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem SIGOTIK</p>
    </div>
</body>
</html>
