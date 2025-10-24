@extends('layouts.dashboard')

@section('title', 'Perubahan Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Perubahan Anggaran</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola perubahan anggaran untuk setiap periode</p>
        </div>
        <button onclick="showAddForm()" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Perubahan
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Periode</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Total Anggaran</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Keterangan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">User Input</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tgl Input</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status Approval</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">User Approval</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tgl Approval</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody" class="bg-white dark:bg-gray-800">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
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
                        <h3 id="anggaranModalLabel" class="text-xl font-semibold text-gray-900 dark:text-white">Form Perubahan Anggaran</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data berikut untuk membuat perubahan anggaran</p>
                    </div>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <form id="anggaranForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="periode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" id="periode" name="periode" required>
                                    <option value="">Pilih Periode</option>
                                    @for($i = 2020; $i <= date('Y') + 5; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                </select>
                            </div>
                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" id="keterangan" name="keterangan" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Anggaran per UPT</h4>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                        <thead style="background-color: #568fd2;">
                                            <tr>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nama UPT</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Anggaran (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="uptTableBody" class="bg-white dark:bg-gray-800">
                                            <!-- Data UPT akan diisi via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="totalAnggaran" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Total Anggaran</label>
                                    <input type="text" class="w-full px-4 py-3 text-lg font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-2 border-blue-300 dark:border-blue-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="totalAnggaran" readonly>
                                </div>
                            </div>
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
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Perubahan Anggaran</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lihat detail perubahan anggaran per UPT</p>
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

        // Auto-filter on change
        $('#status, #dateFrom, #dateTo').change(function() {
            loadData();
        });

        // Search on enter
        $('#search').keypress(function(e) {
            if (e.which == 13) {
                loadData();
            }
        });

        // Search on input with debounce
        let searchTimeout;
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadData();
            }, 500);
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
        // Show loading in table
        $('#dataTableBody').html(`
            <tr>
                <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                        <span class="ml-2">Memuat data...</span>
                    </div>
                </td>
            </tr>
        `);

        // Get filter parameters
        const search = $('#search').val();
        const status = $('#status').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        $.get('{{ route("anggaran.perubahan-anggaran.data") }}', {
            search: search
            , status: status
            , date_from: dateFrom
            , date_to: dateTo
        }, function(response) {
            if (!response.data || response.data.length === 0) {
                $('#dataTableBody').html(`
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data perubahan anggaran
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            response.data.forEach(function(item, index) {
                const statusBadge = item.statusanggaran == 1 ?
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Sudah Di Setujui</span>' :
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Belum Disetujui</span>';

                const tanggalInput = new Date(item.tanggal_input).toLocaleDateString('id-ID');
                const tanggalApproval = item.tanggal_app ? new Date(item.tanggal_app).toLocaleDateString('id-ID') : '-';
                const totalAnggaran = 'Rp. ' + new Intl.NumberFormat('id-ID').format(item.total_anggaran || 0);

                // Format periode dengan perubahan ke
                const periodeDisplay = item.perubahan_ke > 0 ?
                    `${item.periode}<br><span class="text-xs text-gray-500">Perubahan Ke - ${item.perubahan_ke}</span>` :
                    item.periode;

                let actions = '<div class="flex items-center justify-end space-x-1">';

                // Tombol Upload/Pengajuan (biru dengan icon cloud-upload)
                // Logika sesuai project_ci: hanya tampil untuk status 0 atau status 1 yang merupakan perubahan terakhir
                if (item.statusanggaran == 0) {
                    // Status "Belum Disetujui" - selalu tampilkan tombol
                    actions += '<button onclick="uploadAnggaran(\'' + item.periode + '\', \'' + item.perubahan_ke + '\')" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Pengajuan Anggaran"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg></button>';
                } else if (item.statusanggaran == 1 && item.is_latest_approved) {
                    // Status "Sudah Disetujui" - hanya tampilkan jika ini perubahan terakhir yang sudah disetujui
                    actions += '<button onclick="uploadAnggaran(\'' + item.periode + '\', \'' + item.perubahan_ke + '\')" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Pengajuan Anggaran"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg></button>';
                }
                // Jika status = 1 tapi bukan perubahan terakhir, tombol tidak ditampilkan

                // Tombol Edit (kuning dengan icon pencil) - hanya untuk status belum disetujui
                if (item.statusanggaran == 0) {
                    actions += '<button onclick="editAnggaran(\'' + item.periode + '\', \'' + item.perubahan_ke + '\')" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit Anggaran"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>';
                }

                // Tombol Lihat (abu-abu dengan icon eye) - selalu ada
                actions += '<button onclick="viewAnggaran(\'' + item.periode + '\', \'' + item.perubahan_ke + '\')" class="p-2 text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 hover:border-gray-300 dark:bg-gray-900/30 dark:text-gray-400 dark:hover:bg-gray-900/50 dark:border-gray-700 dark:hover:border-gray-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>';

                actions += '</div>';

                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${periodeDisplay}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${totalAnggaran}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.keterangan || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.user_input ? (item.user_input.nama_lengkap || item.user_input.username) : '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${tanggalInput}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            ${statusBadge}
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.user_app || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${tanggalApproval}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            ${actions}
                        </td>
                    </tr>
                `;
            });
            $('#dataTableBody').html(html);
        }).fail(function(xhr, status, error) {
            console.error('Error loading data:', error);
            $('#dataTableBody').html(`
                <tr>
                    <td colspan="10" class="px-6 py-4 text-center text-red-500">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Error loading data: ${error}
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    function loadUptOptions() {
        $.get('{{ route("anggaran.upt-options") }}', function(data) {
            let html = '';
            data.forEach(function(upt, index) {
                html += '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">';
                html += '<td class="px-4 py-2 text-center border border-gray-300 dark:border-gray-600">' + (index + 1) + '</td>';
                html += '<td class="px-4 py-2 border border-gray-300 dark:border-gray-600">' + upt.nama_upt + '</td>';
                html += '<td class="px-4 py-3 border border-gray-300 dark:border-gray-600">';
                html += '<input type="hidden" name="anggaran_data[' + index + '][m_upt_code]" value="' + upt.id + '">';
                html += '<div class="relative">';
                html += '<span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">Rp</span>';
                html += '<input type="text" pattern="[0-9]*" inputmode="numeric" class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white anggaran-input" name="anggaran_data[' + index + '][anggaran]" data-index="' + index + '" data-upt-code="' + upt.id + '" placeholder="0">';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            });
            $('#uptTableBody').html(html);

            // Format number input manually
            $('.anggaran-input').on('input', function() {
                let value = $(this).val().replace(/[^\d]/g, '');
                $(this).val(value);
                calculateTotal();
            });

            // Prevent non-numeric input
            $('.anggaran-input').on('keypress', function(e) {
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

            // Calculate total when input changes
            $('.anggaran-input').on('keyup change', calculateTotal);

            // Fill edit data if available
            if (window.editData && window.editData.data) {
                console.log('Filling edit data...'); // Debug log
                window.editData.data.forEach(function(item, index) {
                    console.log('Looking for UPT code:', item.m_upt_code, 'with anggaran:', item.anggaran); // Debug log
                    // Find the input field for this UPT code using data-upt-code attribute
                    $('.anggaran-input').each(function() {
                        if ($(this).data('upt-code') == item.m_upt_code) {
                            console.log('Found matching input, setting value:', item.anggaran); // Debug log
                            $(this).val(item.anggaran);
                        }
                    });
                });
                // Clear edit data after use
                window.editData = null;
            }

            // Calculate initial total
            calculateTotal();
        }).fail(function(xhr, status, error) {
            console.error('Error loading UPT options:', error);
            $('#uptTableBody').html('<tr><td colspan="3" class="text-center text-red-500">Error loading UPT data</td></tr>');
        });
    }

    function calculateTotal() {
        let total = 0;
        $('.anggaran-input').each(function() {
            let value = $(this).val();
            // Remove all non-numeric characters
            value = value.replace(/[^\d]/g, '');
            if (value !== '' && !isNaN(value)) {
                total += parseInt(value);
            }
        });
        $('#totalAnggaran').val('Rp. ' + new Intl.NumberFormat('id-ID').format(total));
    }

    function showAddForm() {
        // Clear any edit data
        window.editData = null;

        $('#anggaranModalLabel').text('Tambah Perubahan Anggaran');
        $('#anggaranForm')[0].reset();
        $('#totalAnggaran').val('Rp. 0');
        loadUptOptions(); // Load UPT options when modal opens
        $('#anggaranModal').removeClass('hidden');
    }

    function closeModal() {
        $('#anggaranModal').addClass('hidden');
    }

    function closeViewModal() {
        $('#viewModal').addClass('hidden');
    }

    function saveAnggaran() {
        let formData = $('#anggaranForm').serializeArray();
        let anggaranData = [];

        // Collect anggaran data
        $('.anggaran-input').each(function() {
            let value = $(this).val().replace(/,/g, '');
            if (value !== '' && !isNaN(value)) {
                // Get the UPT code from data-upt-code attribute
                let uptCode = $(this).data('upt-code');
                if (uptCode) {
                    anggaranData.push({
                        m_upt_code: uptCode
                        , anggaran: parseFloat(value)
                    });
                }
            }
        });

        let data = {
            periode: $('#periode').val()
            , keterangan: $('#keterangan').val()
            , anggaran_data: anggaranData
        };

        // Determine if this is edit or create
        let isEdit = $('#anggaranModalLabel').text().includes('Edit');
        let url = isEdit ?
            '{{ route("anggaran.perubahan-anggaran.update") }}' :
            '{{ route("anggaran.perubahan-anggaran.create") }}';

        // Add perubahan_ke for edit mode
        if (isEdit) {
            data.perubahan_ke = 0; // Default for edit mode
        }

        $.ajax({
            url: url
            , type: 'POST'
            , data: data
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

    function viewAnggaran(periode, perubahanKe) {
        console.log('Viewing anggaran for periode:', periode, 'perubahanKe:', perubahanKe); // Debug log

        // Show modal immediately
        $('#viewModal').removeClass('hidden');
        console.log('Modal should be visible now'); // Debug log

        // Show loading in modal
        $('#viewModalBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.perubahan-anggaran.view", [":periode", ":perubahanKe"]) }}'.replace(':periode', periode).replace(':perubahanKe', perubahanKe), function(data) {
            console.log('View data received:', data); // Debug log

            let html = '<div class="overflow-x-auto">';
            html += '<table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">';
            html += '<thead style="background-color: #568fd2;">';
            html += '<tr>';
            html += '<th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No</th>';
            html += '<th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">UPT</th>';
            html += '<th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Anggaran (Rp)</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody class="bg-white dark:bg-gray-800">';

            if (data.data && data.data.length > 0) {
                data.data.forEach(function(item, index) {
                    html += '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">';
                    html += '<td class="px-4 py-3 text-center border border-gray-300 dark:border-gray-600">' + (index + 1) + '</td>';
                    html += '<td class="px-4 py-3 border border-gray-300 dark:border-gray-600">' + (item.upt ? item.upt.nama : '-') + '</td>';
                    html += '<td class="px-4 py-3 text-right border border-gray-300 dark:border-gray-600 font-medium">Rp. ' + new Intl.NumberFormat('id-ID').format(item.anggaran) + '</td>';
                    html += '</tr>';
                });
            } else {
                html += '<tr>';
                html += '<td colspan="3" class="px-4 py-3 text-center border border-gray-300 dark:border-gray-600 text-gray-500">Tidak ada data</td>';
                html += '</tr>';
            }

            html += '</tbody></table></div>';
            $('#viewModalBody').html(html);
            console.log('Modal content updated'); // Debug log
        }).fail(function(xhr, status, error) {
            console.error('Error loading view data:', error);
            $('#viewModalBody').html('<div class="text-center py-8 text-red-500">Gagal memuat data detail</div>');
            toastr.error('Gagal memuat data detail');
        });
    }

    function editAnggaran(periode, perubahanKe) {
        $.get('{{ route("anggaran.perubahan-anggaran.edit", [":periode", ":perubahanKe"]) }}'.replace(':periode', periode).replace(':perubahanKe', perubahanKe), function(data) {
            console.log('Edit data received:', data); // Debug log

            // Store the data globally for later use
            window.editData = data;

            $('#periode').val(data.data[0].periode);
            $('#keterangan').val(data.data[0].keterangan);

            // Load UPT options first
            loadUptOptions();

            $('#anggaranModalLabel').text('Edit Perubahan Anggaran');
            $('#anggaranModal').removeClass('hidden');
        });
    }

    function uploadAnggaran(periode, perubahanKe) {
        // Fungsi untuk mengajukan perubahan anggaran
        if (confirm('Apakah Anda yakin ingin mengajukan perubahan anggaran ini?')) {
            $.ajax({
                url: '{{ route("anggaran.perubahan-anggaran.upload", [":periode", ":perubahanKe"]) }}'.replace(':periode', periode).replace(':perubahanKe', perubahanKe)
                , type: 'POST'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        toastr.success('Pengajuan perubahan anggaran berhasil');
                        loadData();
                    } else {
                        toastr.error(response.message);
                    }
                }
                , error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Gagal mengajukan perubahan anggaran');
                }
            });
        }
    }

    function deleteAnggaran(periode, perubahanKe) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '{{ route("anggaran.perubahan-anggaran.delete", [":periode", ":perubahanKe"]) }}'.replace(':periode', periode).replace(':perubahanKe', perubahanKe)
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
