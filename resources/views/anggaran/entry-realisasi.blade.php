@extends('layouts.dashboard')

@section('title', 'Entry Tagihan BBM')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Entry Tagihan BBM</h1>
            <p class="text-gray-600 dark:text-gray-400">Input tagihan BBM untuk setiap UPT</p>
        </div>
        <button onclick="showAddForm()" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Tagihan
        </button>
    </div>

    <!-- Filter and Data Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cari Periode/Keterangan</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari periode, keterangan..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-40">
                    <label for="status" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Filter Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="0">Belum Disetujui</option>
                        <option value="1">Disetujui</option>
                    </select>
                </div>

                <!-- Date From -->
                <div class="w-full sm:w-40">
                    <label for="dateFrom" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dari Tanggal</label>
                    <input type="date" id="dateFrom" name="dateFrom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Date To -->
                <div class="w-full sm:w-40">
                    <label for="dateTo" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Sampai Tanggal</label>
                    <input type="date" id="dateTo" name="dateTo" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Filter Button -->
                <div class="w-full sm:w-auto">
                    <button type="button" id="filterBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                        </svg>
                        Filter
                    </button>
                </div>

                <!-- Reset Button -->
                <div class="w-full sm:w-auto">
                    <button type="button" id="resetBtn" class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 hover:border-gray-400 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:border-gray-600 dark:hover:border-gray-500 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kode UPT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nama UPT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tgl Tagihan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No Tagihan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Penyedia</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Quantity (Liter)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Total (Rp)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody" class="bg-white dark:bg-gray-800">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden items-center justify-center py-8">
        <div class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="anggaranModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 id="anggaranModalLabel" class="text-xl font-semibold text-gray-900 dark:text-white">Form Entry Tagihan BBM</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data berikut untuk input tagihan BBM</p>
                    </div>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <form id="anggaranForm">
                        <!-- UPT Info Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Informasi UPT</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Kode UPT</label>
                                    <input type="text" id="kode_upt" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300" readonly>
                                    <input type="hidden" id="real_kode_upt" name="real_kode_upt">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama UPT</label>
                                    <input type="text" id="nama_upt" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300" readonly>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Alamat UPT</label>
                                    <input type="text" id="alamat_upt" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- SO Section -->
                        <div class="mb-6">
                            <label for="no_so" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                NOMOR SO<br>
                                <span class="text-red-500 text-xs">*) Jika Nomor SO > 1 pakai koma spasi(, ). Contoh : 4013285342, 40130443704, 4013157191, 4013202417, ...dst</span>
                            </label>
                            <div class="flex gap-3">
                                <input type="text" class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white shadow-sm" id="no_so" name="no_so" placeholder="Masukkan nomor SO (kosongkan untuk semua data)">
                                <button type="button" onclick="caridata(1)" class="inline-flex items-center px-6 py-3 text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 hover:border-blue-700 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    CARI DATA
                                </button>
                            </div>
                        </div>

                        <!-- SO Data Display -->
                        <div id="fieldso_1" class="mb-6"></div>

                        <!-- Form Fields Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="no_tagihan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NOMOR TAGIHAN <span class="text-red-500">*</span></label>
                                <input type="text" id="no_tagihan" name="no_tagihan" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300" readonly>
                            </div>
                            <div>
                                <label for="no_spt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NOMOR SPT</label>
                                <input type="text" id="no_spt" name="no_spt" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="tagihanke" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TAGIHAN KE</label>
                                <input type="text" id="tagihanke" name="tagihanke" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="tgl_invoice" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TANGGAL TAGIHAN <span class="text-red-500">*</span></label>
                                <input type="date" id="tgl_invoice" name="tgl_invoice" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="penyedia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PENYEDIA <span class="text-red-500">*</span></label>
                                <input type="text" id="penyedia" name="penyedia" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">QUANTITY (Liter) <span class="text-red-500">*</span></label>
                                <input type="text" id="quantity" name="quantity" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300" readonly>
                                <input type="hidden" id="real_quantity" name="real_quantity">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="harga" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">HARGA TOTAL (Rp) <span class="text-red-500">*</span></label>
                                <input type="text" id="harga" name="harga" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300" readonly>
                                <input type="hidden" id="real_harga" name="real_harga">
                            </div>
                            <div>
                                <label for="hargaperliter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">HARGA PER LITER</label>
                                <input type="text" id="hargaperliter" name="hargaperliter" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300" readonly>
                                <input type="hidden" id="real_hargaperliter" name="real_hargaperliter">
                            </div>
                        </div>


                        <!-- File Upload Section -->
                        <div class="mb-6">
                            <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UPLOAD FILE</label>
                            <input type="file" name="images" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" multiple>
                        </div>
                    </form>
                </div>
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 hover:border-gray-400 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:border-gray-600 dark:hover:border-gray-500 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </button>
                    <button type="button" onclick="saveAnggaran()" class="px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 hover:border-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 dark:border-blue-600 dark:hover:border-blue-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Realisasi</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lihat detail realisasi per UPT</p>
                    </div>
                    <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-6" id="viewModalBody">
                    <!-- Content akan diisi via JavaScript -->
                </div>
                <div class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
                    <button onclick="closeViewModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 hover:border-gray-400 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:border-gray-600 dark:hover:border-gray-500 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- AutoNumeric CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autonumeric.css">
<!-- AutoNumeric JS -->
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autonumeric.min.js"></script>

<script>
    // Configure Toastr
    toastr.options = {
        "closeButton": true
        , "debug": false
        , "newestOnTop": true
        , "progressBar": true
        , "positionClass": "toast-top-right"
        , "preventDuplicates": false
        , "onclick": null
        , "showDuration": "300"
        , "hideDuration": "1000"
        , "timeOut": "5000"
        , "extendedTimeOut": "1000"
        , "showEasing": "swing"
        , "hideEasing": "linear"
        , "showMethod": "fadeIn"
        , "hideMethod": "fadeOut"
    };

    $(document).ready(function() {
        loadData();
        loadUptOptions();

        // Setup date pickers
        setupDatePickers();

        // Filter functionality
        $('#filterBtn').click(function() {
            loadData();
        });

        $('#resetBtn').click(function() {
            $('#filterForm')[0].reset();
            loadData();
        });

        // Search on enter
        $('#search').keypress(function(e) {
            if (e.which == 13) {
                loadData();
            }
        });

        // UPT change handler
        $('#m_upt_code').change(function() {
            getValAnggaran();
        });

        // Date change handler
        $('#tanggal_trans').change(function() {
            getValNomAwal();
        });

        // Nominal input handler
        $('#nominal').on('input', function() {
            calculateSisaPagu();
        });
    });

    function setupDatePickers() {
        // Ensure date inputs work properly
        $('input[type="date"]').each(function() {
            const $input = $(this);

            // Add click event to ensure date picker opens
            $input.on('click', function(e) {
                e.preventDefault();
                this.showPicker && this.showPicker();
            });

            // Add focus event as backup
            $input.on('focus', function() {
                this.showPicker && this.showPicker();
            });

            // Add touch handler for mobile
            $input.on('touchstart', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add keyboard handler
            $input.on('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (this.showPicker) {
                        this.showPicker();
                    }
                }
            });

            // Add double-click handler
            $input.on('dblclick', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add mousedown handler
            $input.on('mousedown', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Ensure proper styling
            $input.css({
                'cursor': 'pointer'
                , 'background-color': 'transparent'
            });
        });
    }

    function loadData() {
        $('#loadingIndicator').removeClass('hidden');

        // Get filter parameters
        const search = $('#search').val();
        const status = $('#status').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        $.get('{{ route("anggaran.entry-realisasi.data") }}', {
            search: search
            , status: status
            , date_from: dateFrom
            , date_to: dateTo
        }, function(response) {
            $('#loadingIndicator').addClass('hidden');

            if (!response.data || response.data.length === 0) {
                $('#dataTableBody').html(`
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data realisasi
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            response.data.forEach(function(item, index) {
                const statusBadge = item.statustagihan == 1 ?
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Disetujui</span>' :
                    item.statustagihan == 3 ?
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Dibatalkan</span>' :
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Belum Disetujui</span>';

                const tanggalTrans = new Date(item.tanggal_invoice).toLocaleDateString('id-ID');
                const total = 'Rp. ' + new Intl.NumberFormat('id-ID').format(item.total || 0);

                let actions = '<div class="flex items-center justify-end space-x-1">';
                actions += '<button onclick="viewRealisasi(\'' + item.tagihan_id + '\')" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>';
                if (item.statustagihan == 0) {
                    actions += '<button onclick="editRealisasi(\'' + item.tagihan_id + '\')" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>';
                    actions += '<button onclick="deleteRealisasi(\'' + item.tagihan_id + '\')" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
                }
                actions += '</div>';

                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${item.m_upt_code || '-'}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.upt_nama || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${tanggalTrans}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.no_tagihan || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${item.penyedia || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.quantity || 0}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${total}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            ${statusBadge}
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            ${actions}
                        </td>
                    </tr>
                `;
            });
            $('#dataTableBody').html(html);
        }).fail(function(xhr, status, error) {
            $('#loadingIndicator').addClass('hidden');
            console.error('Error loading data:', error);
            toastr.error('Gagal memuat data');
        });
    }

    function loadUptOptions() {
        $.get('{{ route("anggaran.upt-options") }}', function(data) {
            let html = '<option value="">Pilih UPT</option>';
            data.forEach(function(upt) {
                html += '<option value="' + upt.id + '">' + upt.nama_upt + '</option>';
            });
            $('#m_upt_code').html(html);

            // Format nominal input
            $('#nominal').on('input', function() {
                let value = $(this).val().replace(/[^\d]/g, '');
                $(this).val(value);
                calculateSisaPagu();
            });

            // Prevent non-numeric input
            $('#nominal').on('keypress', function(e) {
                // Allow: backspace, delete, tab, escape, enter
                if ([46, 8, 9, 27, 13, 110].indexOf(e.keyCode) !== -1 ||
                    // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true)) {
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            // Fill edit data if available
            if (window.editData) {
                $('#tanggal_trans').val(window.editData.tanggal_trans);
                $('#m_upt_code').val(window.editData.m_upt_code);
                $('#nominal').val(window.editData.nominal);
                $('#nomor_surat').val(window.editData.nomor_surat);
                $('#keterangan').val(window.editData.keterangan);

                // Get anggaran and nominal awal for edit
                getValAnggaran();
                getValNomAwal();

                // Clear edit data after use
                window.editData = null;
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading UPT options:', error);
            toastr.error('Gagal memuat data UPT');
        });
    }

    function getValAnggaran() {
        const kodeUpt = $('#m_upt_code').val();
        if (!kodeUpt) {
            $('#anggaran_tersedia').val('Rp. 0');
            return;
        }

        $.ajax({
            type: "GET"
            , url: "{{ route('anggaran.anggaran-data') }}"
            , data: {
                kode_upt: kodeUpt
            }
            , dataType: "json"
            , success: function(data) {
                $('#anggaran_tersedia').val('Rp. ' + new Intl.NumberFormat('id-ID').format(data.anggaran || 0));
                getValNomAwal();
            }
            , error: function() {
                $('#anggaran_tersedia').val('Rp. 0');
            }
        });
    }

    function getValNomAwal() {
        const tanggalTrans = $('#tanggal_trans').val();
        const kodeUpt = $('#m_upt_code').val();

        if (!tanggalTrans || !kodeUpt) {
            $('#nominal_awal').val('Rp. 0');
            calculateSisaPagu();
            return;
        }

        const dataForm = {
            tanggal_trans: tanggalTrans
            , kode_upt: kodeUpt
        };

        $.ajax({
            type: "GET"
            , url: "{{ route('anggaran.nominal-awal') }}"
            , data: dataForm
            , dataType: "json"
            , success: function(data) {
                $('#nominal_awal').val('Rp. ' + new Intl.NumberFormat('id-ID').format(data.nominal || 0));
                calculateSisaPagu();
            }
            , error: function() {
                $('#nominal_awal').val('Rp. 0');
                calculateSisaPagu();
            }
        });
    }

    function calculateSisaPagu() {
        const anggaran = parseFloat($('#anggaran_tersedia').val().replace(/[^\d]/g, '')) || 0;
        const nominalAwal = parseFloat($('#nominal_awal').val().replace(/[^\d]/g, '')) || 0;
        const nominalRubah = parseFloat($('#nominal').val().replace(/[^\d]/g, '')) || 0;

        const sisaPagu = anggaran + nominalAwal + nominalRubah;
        $('#sisa_pagu').val('Rp. ' + new Intl.NumberFormat('id-ID').format(sisaPagu));
    }

    function showAddForm() {
        // Clear any edit data
        window.editData = null;

        $('#anggaranModalLabel').text('Tambah Tagihan BBM');
        $('#anggaranForm')[0].reset();
        loadUptInfo(); // Load UPT info when modal opens
        $('#anggaranModal').removeClass('hidden');
    }

    function closeModal() {
        $('#anggaranModal').addClass('hidden');
    }

    function closeViewModal() {
        $('#viewModal').addClass('hidden');
    }

    function saveAnggaran() {
        let data = new FormData();

        // Add file data
        var file_data = $('input[name="images"]')[0].files;
        for (var i = 0; i < file_data.length; i++) {
            data.append("images[]", file_data[i]);
        }

        // Add form data
        data.append('no_so', $('#no_so').val());
        data.append('quantity', $('#real_quantity').val());
        data.append('harga', $('#real_harga').val());
        data.append('hargaperliter', $('#real_hargaperliter').val());
        data.append('kode_upt', $('#real_kode_upt').val());

        // Determine if this is edit or create
        let isEdit = $('#anggaranModalLabel').text().includes('Edit');
        let url = isEdit ?
            '{{ route("anggaran.entry-realisasi.update") }}' :
            '{{ route("anggaran.entry-realisasi.create") }}';

        // Add ID for edit
        if (isEdit && window.editId) {
            data.append('id', window.editId);
        }

        $.ajax({
            url: url
            , type: 'POST'
            , data: data
            , processData: false
            , contentType: false
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(response) {
                if (response.success) {
                    toastr.success('Data berhasil disimpan');
                    $('#anggaranModal').addClass('hidden');
                    loadData();
                } else {
                    toastr.error(response.message);
                }
            }
            , error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    }

    // Load UPT Info (similar to project_ci)
    function loadUptInfo() {
        $.ajax({
            type: "GET"
            , url: "{{ route('anggaran.upt-info') }}"
            , dataType: "json"
            , success: function(data) {
                $('#kode_upt').val(data.code);
                $('#real_kode_upt').val(data.code);
                $('#nama_upt').val(data.nama);
                $('#alamat_upt').val(data.alamat);

                // Generate nomor tagihan
                generateNomorTagihan();
            }
            , error: function() {
                toastr.error('Gagal memuat informasi UPT');
            }
        });
    }

    // Generate Nomor Tagihan
    function generateNomorTagihan() {
        $.ajax({
            type: "GET"
            , url: "{{ route('anggaran.generate-nomor-tagihan') }}"
            , dataType: "json"
            , success: function(data) {
                $('#no_tagihan').val(data.nomor_tagihan);
            }
            , error: function() {
                toastr.error('Gagal generate nomor tagihan');
            }
        });
    }

    // Search SO Data (similar to project_ci)
    function caridata(call) {
        var id = $('#no_so').val();
        var multino = id.replace(/, /g, "x");

        // If input is empty, use 0 to get all data
        if (id == '' || id.trim() == '') {
            multino = 0;
        }

        $('#quantity').val(0);
        $('#real_quantity').val(0);
        $('#harga').val('');
        $('#real_harga').val('');
        $('#hargaperliter').val(0);
        $('#real_hargaperliter').val(0);

        // Use proper route with parameter
        var url = '{{ route("anggaran.get-so-data", ":multino") }}';
        url = url.replace(':multino', multino);
        $('#fieldso_' + call).load(url);
    }


    // Auto calculate harga per liter
    $(document).on('change', '#quantity, #harga', function() {
        var kuan = $('#quantity').val();
        var hrg = $('#harga').val();

        if (kuan && hrg) {
            var kuantiti = kuan.replace(/\./g, '');
            var harga = hrg.replace(/,/g, '');

            if ($.isNumeric(kuantiti) && $.isNumeric(harga)) {
                var hargaperliter = parseFloat(harga) / parseFloat(kuantiti);
                $('#real_quantity').val(kuantiti);
                $('#real_harga').val(harga);
                $('#real_hargaperliter').val(hargaperliter);
                $('#hargaperliter').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                    , maximumFractionDigits: 2
                }).format(hargaperliter));
            }
        }
    });

    // Add commas function (similar to project_ci)
    function addCommas(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Handle checkbox changes for SO selection
    $(document).on('change', '#check_alls', function() {
        var isChecked = $(this).is(':checked');
        $('.custom-control-input').prop('checked', isChecked);
        calculateSelectedSO();
    });

    $(document).on('change', '.custom-control-input', function() {
        var totalCheckboxes = $('.custom-control-input').length;
        var checkedCheckboxes = $('.custom-control-input:checked').length;

        if (checkedCheckboxes === totalCheckboxes) {
            $('#check_alls').prop('checked', true);
        } else {
            $('#check_alls').prop('checked', false);
        }

        calculateSelectedSO();
    });

    // Calculate selected SO data
    function calculateSelectedSO() {
        var totalQuantity = 0;
        var totalHarga = 0;

        $('.custom-control-input:checked').each(function() {
            var row = $(this).closest('tr');
            var volume = parseFloat(row.find('td:eq(5)').text().replace(/[^\d]/g, '')) || 0;
            var harga = parseFloat(row.find('input[type="text"]').val().replace(/[^\d]/g, '')) || 0;

            totalQuantity += volume;
            totalHarga += harga;
        });

        // Update form fields
        $('#quantity').val(totalQuantity);
        $('#real_quantity').val(totalQuantity);
        $('#harga').val(addCommas(totalHarga));
        $('#real_harga').val(totalHarga);

        // Calculate harga per liter
        if (totalQuantity > 0) {
            var hargaperliter = totalHarga / totalQuantity;
            $('#real_hargaperliter').val(hargaperliter);
            $('#hargaperliter').val(addCommas(hargaperliter));
        } else {
            $('#real_hargaperliter').val(0);
            $('#hargaperliter').val('0');
        }
    }

    function viewRealisasi(id) {
        console.log('Viewing realisasi for ID:', id);

        // Show modal immediately
        $('#viewModal').removeClass('hidden');

        // Show loading in modal
        $('#viewModalBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.entry-realisasi.view", [":id"]) }}'.replace(':id', id), function(response) {
            console.log('View data received:', response);

            const data = response.data;
            let html = '<div class="space-y-4">';
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Transaksi</label><p class="text-sm text-gray-900 dark:text-white">' + new Date(data.tanggal_trans).toLocaleDateString('id-ID') + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">UPT</label><p class="text-sm text-gray-900 dark:text-white">' + (data.upt ? data.upt.nama : '-') + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Surat</label><p class="text-sm text-gray-900 dark:text-white">' + (data.nomor_surat || '-') + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nominal</label><p class="text-sm text-gray-900 dark:text-white font-bold">Rp. ' + new Intl.NumberFormat('id-ID').format(data.nominal || 0) + '</p></div>';
            html += '</div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label><p class="text-sm text-gray-900 dark:text-white">' + (data.keterangan || '-') + '</p></div>';
            html += '</div>';

            $('#viewModalBody').html(html);
        }).fail(function(xhr, status, error) {
            console.error('Error loading view data:', error);
            $('#viewModalBody').html('<div class="text-center py-8 text-red-500">Gagal memuat data detail</div>');
            toastr.error('Gagal memuat data detail');
        });
    }

    function editRealisasi(id) {
        $.get('{{ route("anggaran.entry-realisasi.form", [":id"]) }}'.replace(':id', id), function(response) {
            console.log('Edit data received:', response);

            // Store the data globally for later use
            window.editData = response.data;
            window.editId = id;

            // Load UPT options first
            loadUptOptions();

            $('#anggaranModalLabel').text('Edit Entry Realisasi');
            $('#anggaranModal').removeClass('hidden');
        });
    }

    function deleteRealisasi(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '{{ route("anggaran.entry-realisasi.delete", [":id"]) }}'.replace(':id', id)
                , type: 'DELETE'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        toastr.success('Data berhasil dihapus');
                        loadData();
                    } else {
                        toastr.error(response.message);
                    }
                }
                , error: function(xhr) {
                    toastr.error(xhr.responseJSON.message);
                }
            });
        }
    }

</script>
@endsection
