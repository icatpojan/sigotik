@extends('layouts.dashboard')

@section('title', 'Manajemen BBM')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen BBM</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola data Berita Acara BBM</p>
        </div>
        <button id="createBbmBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah BBM
        </button>
    </div>

    <!-- Filter and BBM Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari BBM</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari nomor surat, kapal..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('search') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status BA Filter -->
                <div class="w-full sm:w-40">
                    <label for="status_ba" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Status BA</label>
                    <select id="status_ba" name="status_ba" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Status BA</option>
                        <option value="0">BA Default</option>
                        <option value="1">BA Penerimaan BBM</option>
                        <option value="2">BA Peminjaman BBM</option>
                        <option value="3">BA Penitipan BBM</option>
                        <option value="4">BA Pemeriksaan Sarana Pengisian</option>
                        <option value="5">BA Penerimaan Hibah BBM</option>
                        <option value="6">BA Sebelum Pelayaran</option>
                        <option value="7">BA Penggunaan BBM</option>
                        <option value="8">BA Pengembalian BBM</option>
                        <option value="9">BA Penerimaan Pengembalian BBM</option>
                        <option value="10">BA Penerimaan Pinjaman BBM</option>
                        <option value="11">BA Pengembalian Pinjaman BBM</option>
                        <option value="12">BA Pemberi Hibah BBM Kapal Pengawas</option>
                        <option value="13">BA Penerima Hibah BBM Kapal Pengawas</option>
                        <option value="14">BA Penerima Hibah BBM Instansi Lain</option>
                        <option value="15">BA Akhir Bulan</option>
                    </select>
                </div>

                <!-- Status Transaksi Filter -->
                <div class="w-full sm:w-40">
                    <label for="status_trans" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Status</label>
                    <select id="status_trans" name="status_trans" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="0">Input</option>
                        <option value="1">Approval</option>
                        <option value="2">Batal</option>
                    </select>
                </div>

                <!-- Kapal Filter -->
                <div class="w-full sm:w-40">
                    <label for="kapal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Kapal</label>
                    <select id="kapal" name="kapal" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Kapal</option>
                        @foreach($kapals as $kapal)
                        <option value="{{ $kapal->code }}">{{ $kapal->nama_kapal }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From Filter -->
                <div class="w-full sm:w-32">
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                    <input type="date" id="date_from" name="date_from" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Date To Filter -->
                <div class="w-full sm:w-32">
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input type="date" id="date_to" name="date_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Per Page Selector -->
                <div class="w-full sm:w-32">
                    <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Per Halaman</label>
                    <select id="perPage" name="per_page" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Clear Filter Button -->
                <div class="w-full sm:w-auto">
                    <button type="button" id="clearFilter" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- BBM Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nomor Surat & Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kapal & Lokasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status BA & Transaksi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Volume BBM</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bbmTableBody" class="bg-white dark:bg-gray-800">
                    <!-- Data akan dimuat via AJAX -->
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                            <div class="flex flex-col items-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data BBM</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan data BBM pertama</p>
                                <button id="createFirstBbmBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Data BBM Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden items-center justify-center py-8">
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span>
            </div>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            <!-- Pagination akan dimuat via AJAX -->
        </div>
    </div>
</div>

<!-- BBM Modal -->
<div id="bbmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-white">Form Tambah BBM</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="bbmForm" class="mt-6">
                @csrf
                <input type="hidden" id="bbmId" name="bbm_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="kapal_code" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kode Kapal</label>
                            <select id="kapal_code" name="kapal_code" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">- PILIH KAPAL -</option>
                                @foreach($kapals as $kapal)
                                <option value="{{ $kapal->code }}">{{ $kapal->nama_kapal }} ({{ $kapal->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="nomor_surat" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nomor Surat</label>
                            <input type="text" id="nomor_surat" name="nomor_surat" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="tanggal_surat" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Surat</label>
                            <input type="date" id="tanggal_surat" name="tanggal_surat" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="jam_surat" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jam Surat</label>
                            <input type="time" id="jam_surat" name="jam_surat" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="zona_waktu_surat" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Zona Waktu</label>
                            <select id="zona_waktu_surat" name="zona_waktu_surat" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="WIB">WIB</option>
                                <option value="WITA">WITA</option>
                                <option value="WIT">WIT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="lokasi_surat" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Lokasi Surat</label>
                            <textarea id="lokasi_surat" name="lokasi_surat" required rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>

                        <div>
                            <label for="status_ba" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status BA</label>
                            <select id="status_ba" name="status_ba" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">- PILIH STATUS BA -</option>
                                <option value="1">BA Penerimaan BBM</option>
                                <option value="2">BA Peminjaman BBM</option>
                                <option value="3">BA Penitipan BBM</option>
                                <option value="4">BA Pemeriksaan Sarana Pengisian</option>
                                <option value="5">BA Penerimaan Hibah BBM</option>
                                <option value="6">BA Sebelum Pelayaran</option>
                                <option value="7">BA Penggunaan BBM</option>
                                <option value="8">BA Pengembalian BBM</option>
                                <option value="9">BA Penerimaan Pengembalian BBM</option>
                                <option value="10">BA Penerimaan Pinjaman BBM</option>
                                <option value="11">BA Pengembalian Pinjaman BBM</option>
                                <option value="12">BA Pemberi Hibah BBM Kapal Pengawas</option>
                                <option value="13">BA Penerima Hibah BBM Kapal Pengawas</option>
                                <option value="14">BA Penerima Hibah BBM Instansi Lain</option>
                                <option value="15">BA Akhir Bulan</option>
                            </select>
                        </div>

                        <div>
                            <label for="volume_sisa" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Volume Sisa (Liter)</label>
                            <input type="number" id="volume_sisa" name="volume_sisa" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="volume_sebelum" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Volume Sebelum (Liter)</label>
                            <input type="number" id="volume_sebelum" name="volume_sebelum" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="volume_pengisian" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Volume Pengisian (Liter)</label>
                            <input type="number" id="volume_pengisian" name="volume_pengisian" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Nakhoda & KKM Section -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Data Nakhoda & KKM</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nakhoda -->
                        <div class="space-y-4">
                            <h5 class="text-md font-medium text-gray-700 dark:text-gray-300">Nakhoda</h5>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="an_nakhoda" name="an_nakhoda" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="an_nakhoda" class="text-sm text-gray-700 dark:text-gray-300">An.</label>
                            </div>

                            <div>
                                <label for="nama_nahkoda" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Nakhoda</label>
                                <input type="text" id="nama_nahkoda" name="nama_nahkoda" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label for="nip_nahkoda" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIP Nakhoda</label>
                                <input type="text" id="nip_nahkoda" name="nip_nahkoda" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- KKM -->
                        <div class="space-y-4">
                            <h5 class="text-md font-medium text-gray-700 dark:text-gray-300">KKM</h5>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="an_kkm" name="an_kkm" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="an_kkm" class="text-sm text-gray-700 dark:text-gray-300">An.</label>
                            </div>

                            <div>
                                <label for="nama_kkm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama KKM</label>
                                <input type="text" id="nama_kkm" name="nama_kkm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label for="nip_kkm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIP KKM</label>
                                <input type="text" id="nip_kkm" name="nip_kkm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" id="submitBtn" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View BBM Modal -->
<div id="viewBbmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail BBM</h3>
                <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="bbmDetails" class="mt-6">
                <!-- BBM details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let currentPage = 1;
    let currentFilters = {};

    $(document).ready(function() {
        // Load initial data
        loadBbmData();

        // Modal controls
        $('#createBbmBtn, #createFirstBbmBtn').click(function() {
            $('#modalTitle').text('Form Tambah BBM');
            $('#bbmForm')[0].reset();
            $('#bbmId').val('');
            $('#bbmModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        $('#closeModal').click(function() {
            $('#bbmModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        $('#closeViewModal').click(function() {
            $('#viewBbmModal').addClass('hidden');
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1; // Reset to first page when filtering
            loadBbmData();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#status_ba').val('');
            $('#status_trans').val('');
            $('#kapal').val('');
            $('#date_from').val('');
            $('#date_to').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadBbmData();
        });

        // BBM form submission
        $('#bbmForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const bbmId = $('#bbmId').val();
            const url = bbmId ? `/bbm/${bbmId}` : '/bbm';
            const method = bbmId ? 'PUT' : 'POST';

            // Add _method for PUT request
            if (bbmId) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , success: function(response) {
                    if (response.success) {
                        $('#bbmModal').addClass('hidden');
                        loadBbmData(); // Reload data
                        showNotification('success', response.message);
                    } else {
                        showNotification('error', response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        // Session expired, redirect to login
                        window.location.href = '/login';
                    } else if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validasi gagal:\n';
                        for (let field in errors) {
                            errorMessage += `- ${errors[field][0]}\n`;
                        }
                        showNotification('error', errorMessage);
                    } else {
                        showNotification('error', 'Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });
    });

    // Function to load BBM data via AJAX
    function loadBbmData(page = 1) {
        currentPage = page;

        // Show loading indicator
        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#bbmTableBody').html('');
        $('#paginationContainer').html('');

        // Get current filter values
        const filters = {
            search: $('#search').val()
            , status_ba: $('#status_ba').val()
            , status_trans: $('#status_trans').val()
            , kapal: $('#kapal').val()
            , date_from: $('#date_from').val()
            , date_to: $('#date_to').val()
            , per_page: $('#perPage').val()
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/bbm/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderBbmTable(response.bbm_data);
                    renderPagination(response.pagination);
                } else {
                    showErrorMessage('Gagal memuat data BBM');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    showErrorMessage('Terjadi kesalahan saat memuat data');
                }
            }
            , complete: function() {
                $('#loadingIndicator').addClass('hidden').removeClass('flex');
            }
        });
    }

    // Function to render BBM table
    function renderBbmTable(bbmData) {
        const tbody = $('#bbmTableBody');
        tbody.html('');

        if (bbmData.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data BBM</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan data BBM pertama</p>
                            <button id="createFirstBbmBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Data BBM Pertama
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        bbmData.forEach(function(bbm) {
            const statusBaText = getStatusBaText(bbm.status_ba);
            const statusTransText = getStatusTransText(bbm.status_trans);
            const tanggalSurat = new Date(bbm.tanggal_surat).toLocaleDateString('id-ID');

            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${bbm.nomor_surat}</div>
                            <div class="text-gray-500 dark:text-gray-400">${tanggalSurat} ${bbm.jam_surat}</div>
                            <div class="text-gray-500 dark:text-gray-400">${bbm.zona_waktu_surat}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${bbm.kapal ? bbm.kapal.nama_kapal : bbm.kapal_code}</div>
                            <div class="text-gray-500 dark:text-gray-400">${bbm.lokasi_surat}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${statusBaText}</div>
                            <div class="text-gray-500 dark:text-gray-400">Status: ${statusTransText}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Sisa: ${bbm.volume_sisa || 0} L</div>
                            <div>Sebelum: ${bbm.volume_sebelum || 0} L</div>
                            <div>Pengisian: ${bbm.volume_pengisian || 0} L</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewBbm(${bbm.trans_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="editBbm(${bbm.trans_id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit BBM">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="generatePdf(${bbm.trans_id})" class="p-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Generate PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteBbm(${bbm.trans_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus BBM">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Function to render pagination
    function renderPagination(pagination) {
        const container = $('#paginationContainer');

        if (pagination.last_page <= 1) {
            container.html('');
            return;
        }

        let paginationHtml = '<div class="flex items-center justify-between">';

        // Info
        paginationHtml += `<div class="text-sm text-gray-700 dark:text-gray-300">
            Menampilkan ${pagination.from || 0} sampai ${pagination.to || 0} dari ${pagination.total} data (${pagination.per_page} per halaman)
        </div>`;

        // Pagination links
        paginationHtml += '<div class="flex space-x-1">';

        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += `<button onclick="loadBbmData(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Sebelumnya
            </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadBbmData(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'} border rounded-md">
                ${i}
            </button>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadBbmData(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Selanjutnya
            </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    // Function to show error message
    function showErrorMessage(message) {
        $('#bbmTableBody').html(`
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-red-500 border border-gray-300 dark:border-gray-600">
                    <div class="flex flex-col items-center py-8">
                        <svg class="w-12 h-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-red-600 mb-2">Error</h3>
                        <p class="text-red-500">${message}</p>
                    </div>
                </td>
            </tr>
        `);
    }

    // Global functions
    function editBbm(id) {
        $.ajax({
            url: `/bbm/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const bbm = response.bbm_data;
                    $('#modalTitle').text('Form Edit BBM');
                    $('#bbmId').val(bbm.trans_id);
                    $('#kapal_code').val(bbm.kapal_code);
                    $('#nomor_surat').val(bbm.nomor_surat);
                    $('#tanggal_surat').val(bbm.tanggal_surat);
                    $('#jam_surat').val(bbm.jam_surat);
                    $('#zona_waktu_surat').val(bbm.zona_waktu_surat);
                    $('#lokasi_surat').val(bbm.lokasi_surat);
                    $('#status_ba').val(bbm.status_ba);
                    $('#volume_sisa').val(bbm.volume_sisa);
                    $('#volume_sebelum').val(bbm.volume_sebelum);
                    $('#volume_pengisian').val(bbm.volume_pengisian);
                    $('#nama_nahkoda').val(bbm.nama_nahkoda);
                    $('#nip_nahkoda').val(bbm.nip_nahkoda);
                    $('#nama_kkm').val(bbm.nama_kkm);
                    $('#nip_kkm').val(bbm.nip_kkm);
                    $('#an_nakhoda').prop('checked', bbm.an_nakhoda == 1);
                    $('#an_kkm').prop('checked', bbm.an_kkm == 1);
                    $('#bbmModal').removeClass('hidden').addClass('flex items-center justify-center');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    window.location.href = '/login';
                } else {
                    showNotification('error', 'Terjadi kesalahan saat mengambil data BBM');
                }
            }
        });
    }

    function viewBbm(id) {
        $.ajax({
            url: `/bbm/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const bbm = response.bbm_data;
                    const bbmDetails = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-xl font-medium text-white">${bbm.nomor_surat.charAt(0)}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">${bbm.nomor_surat}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${new Date(bbm.tanggal_surat).toLocaleDateString('id-ID')} ${bbm.jam_surat}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Kapal:</span>
                                <p class="text-gray-900 dark:text-white">${bbm.kapal ? bbm.kapal.nama_kapal : bbm.kapal_code}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Lokasi:</span>
                                <p class="text-gray-900 dark:text-white">${bbm.lokasi_surat}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Status BA:</span>
                                <p class="text-gray-900 dark:text-white">${getStatusBaText(bbm.status_ba)}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Status Transaksi:</span>
                                <p class="text-gray-900 dark:text-white">${getStatusTransText(bbm.status_trans)}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Volume Sisa:</span>
                                <p class="text-gray-900 dark:text-white">${bbm.volume_sisa || 0} Liter</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Volume Sebelum:</span>
                                <p class="text-gray-900 dark:text-white">${bbm.volume_sebelum || 0} Liter</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Nakhoda:</span>
                                <p class="text-gray-900 dark:text-white">${bbm.an_nakhoda == 1 ? 'An. ' : ''}${bbm.nama_nahkoda || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">KKM:</span>
                                <p class="text-gray-900 dark:text-white">${bbm.an_kkm == 1 ? 'An. ' : ''}${bbm.nama_kkm || '-'}</p>
                            </div>
                        </div>
                    </div>
                `;
                    $('#bbmDetails').html(bbmDetails);
                    $('#viewBbmModal').removeClass('hidden');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    window.location.href = '/login';
                } else {
                    showNotification('error', 'Terjadi kesalahan saat mengambil data BBM');
                }
            }
        });
    }

    function deleteBbm(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data BBM ini?')) {
            $.ajax({
                url: `/bbm/${id}`
                , type: 'POST'
                , data: {
                    _method: 'DELETE'
                    , _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        loadBbmData(currentPage);
                        showNotification('success', response.message);
                    } else {
                        showNotification('error', response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else {
                        showNotification('error', 'Terjadi kesalahan saat menghapus data');
                    }
                }
            });
        }
    }

    function generatePdf(id) {
        $.ajax({
            url: `/bbm/${id}/pdf`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    showNotification('success', 'PDF berhasil dibuat');
                    // TODO: Open PDF in new window
                    // window.open(response.pdf_url, '_blank');
                } else {
                    showNotification('error', response.message);
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    window.location.href = '/login';
                } else {
                    showNotification('error', 'Terjadi kesalahan saat membuat PDF');
                }
            }
        });
    }

    // Helper functions
    function getStatusBaText(status) {
        const statusMap = {
            0: 'BA Default'
            , 1: 'BA Penerimaan BBM'
            , 2: 'BA Peminjaman BBM'
            , 3: 'BA Penitipan BBM'
            , 4: 'BA Pemeriksaan Sarana Pengisian'
            , 5: 'BA Penerimaan Hibah BBM'
            , 6: 'BA Sebelum Pelayaran'
            , 7: 'BA Penggunaan BBM'
            , 8: 'BA Pengembalian BBM'
            , 9: 'BA Penerimaan Pengembalian BBM'
            , 10: 'BA Penerimaan Pinjaman BBM'
            , 11: 'BA Pengembalian Pinjaman BBM'
            , 12: 'BA Pemberi Hibah BBM Kapal Pengawas'
            , 13: 'BA Penerima Hibah BBM Kapal Pengawas'
            , 14: 'BA Penerima Hibah BBM Instansi Lain'
            , 15: 'BA Akhir Bulan'
        };
        return statusMap[status] || 'Unknown';
    }

    function getStatusTransText(status) {
        const statusMap = {
            0: 'Input'
            , 1: 'Approval'
            , 2: 'Batal'
        };
        return statusMap[status] || 'Unknown';
    }

    function showNotification(type, message) {
        // Simple notification - you can enhance this with a proper notification library
        alert(message);
    }

</script>
@endsection
