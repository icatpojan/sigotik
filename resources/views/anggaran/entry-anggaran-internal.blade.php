@extends('layouts.dashboard')

@section('title', 'Entry Anggaran Internal')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Entry Anggaran Internal</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola anggaran internal UPT</p>
        </div>
        <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Anggaran Internal
        </button>
    </div>

    <!-- Filter and Data Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cari Data</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari nomor surat, keterangan..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <button type="button" id="filterBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <button type="button" id="resetBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">UPT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tgl Transaksi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nominal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nomor Surat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Keterangan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">User Input</th>
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

<!-- Create/Edit Modal -->
<div id="formModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalTitle">Tambah Anggaran Internal</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Form input anggaran internal</p>
                    </div>
                    <button onclick="closeFormModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                    <form id="anggaranForm">
                    <div class="mt-6" id="formBody">
                        <!-- Form content akan diisi via JavaScript -->
                            </div>
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 mt-6 space-x-3">
                        <button type="button" onclick="closeFormModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                        Batal
                    </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                        Simpan
                    </button>
                </div>
                </form>
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
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Anggaran Internal</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lihat detail anggaran internal</p>
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
                    <button onclick="closeViewModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
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

    let currentEditId = null;

    $(document).ready(function() {
        loadData();

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

        // Form submission
        $('#anggaranForm').on('submit', function(e) {
            e.preventDefault();
            saveAnggaran();
        });
    });

    function loadData() {
        $('#loadingIndicator').removeClass('hidden');

        // Get filter parameters
        const search = $('#search').val();

        $.get('{{ route("anggaran.entry-anggaran-internal.data") }}', {
            search: search
        }, function(response) {
            $('#loadingIndicator').addClass('hidden');

            if (!response.data || response.data.length === 0) {
                $('#dataTableBody').html(`
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data anggaran internal
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            response.data.forEach(function(item, index) {
                const tanggalTrans = new Date(item.tanggal_trans).toLocaleDateString('id-ID');
                const nominal = new Intl.NumberFormat('id-ID', {
                    style: 'currency'
                    , currency: 'IDR'
                    , minimumFractionDigits: 0
                }).format(item.nominal);

                let statusBadge = '';
                if (item.statusperubahan == 0) {
                    statusBadge = '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Belum Disetujui</span>';
                } else if (item.statusperubahan == 1) {
                    statusBadge = '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Disetujui</span>';
                } else if (item.statusperubahan == 2) {
                    statusBadge = '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Dibatalkan</span>';
                }

                let actions = '<div class="flex items-center justify-end space-x-1">';
                actions += '<button onclick="viewAnggaran(' + item.anggaran_upt_id + ')" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>';

                if (item.statusperubahan == 0) {
                    actions += '<button onclick="editAnggaran(' + item.anggaran_upt_id + ')" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>';
                    actions += '<button onclick="deleteAnggaran(' + item.anggaran_upt_id + ')" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
                }

                actions += '</div>';

                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${item.upt ? item.upt.nama : '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${tanggalTrans}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${nominal}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.nomor_surat}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.keterangan}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            ${statusBadge}
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.user_input ? (item.user_input.nama_lengkap || item.user_input.username) : '-'}</div>
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

    function openCreateModal() {
        currentEditId = null;
        $('#modalTitle').text('Tambah Anggaran Internal');
        loadFormData();
    }

    function loadFormData() {
        $('#formBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.entry-anggaran-internal.form") }}', function(data) {
            let html = '<div class="space-y-4">';

            // UPT Info (Readonly)
            html += '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode UPT</label><input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + data.data.upt.code + '" readonly></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama UPT</label><input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + data.data.upt.nama + '" readonly></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat UPT</label><input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + (data.data.upt.alamat1 || data.data.upt.alamat2 || '') + '" readonly></div>';
            html += '</div>';

            // Anggaran Info (Readonly)
            html += '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anggaran</label><input type="text" id="anggaran" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + new Intl.NumberFormat('id-ID', {
                style: 'currency'
                , currency: 'IDR'
                , minimumFractionDigits: 0
            }).format(data.data.anggaran) + '" readonly></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perubahan UPT</label><input type="text" id="perubahan_upt" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="Rp. 0" readonly></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sisa Pagu</label><input type="text" id="sisa_pagu" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + new Intl.NumberFormat('id-ID', {
                style: 'currency'
                , currency: 'IDR'
                , minimumFractionDigits: 0
            }).format(data.data.anggaran) + '" readonly></div>';
            html += '</div>';

            // Form Input
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Transaksi <span class="text-red-500">*</span></label><input type="date" id="tanggal_trans" name="tanggal_trans" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nominal Perubahan <span class="text-red-500">*</span></label><input type="text" id="nominal" name="nominal" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white currency-input" placeholder="0" required></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Surat <span class="text-red-500">*</span></label><input type="text" id="nomor_surat" name="nomor_surat" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Masukkan nomor surat" required></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan <span class="text-red-500">*</span></label><input type="text" id="keterangan" name="keterangan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Masukkan keterangan" required></div>';
            html += '</div>';

            html += '</div>';

            $('#formBody').html(html);
            $('#formModal').removeClass('hidden');

            // Setup date pickers
            setupDatePickers();

            // Currency formatting
            $('.currency-input').on('input', function() {
                let value = $(this).val().replace(/[^\d]/g, '');
                if (value) {
                    $(this).val(new Intl.NumberFormat('id-ID').format(value));
                    calculateSisaPagu();
                }
            });
        }).fail(function(xhr, status, error) {
            console.error('Error loading form data:', error);
            $('#formBody').html('<div class="text-center py-8 text-red-500">Gagal memuat form</div>');
            toastr.error('Gagal memuat form');
        });
    }

    function loadEditFormData(id) {
        $('#formBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.entry-anggaran-internal.edit", [":id"]) }}'.replace(':id', id), function(data) {
            console.log('Edit data received:', data);
            console.log('Tanggal trans:', data.data.tanggal_trans);
            let html = '<div class="space-y-4">';

            // UPT Info (Readonly)
            html += '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode UPT</label><input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + data.data.upt.code + '" readonly></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama UPT</label><input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + data.data.upt.nama + '" readonly></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat UPT</label><input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + (data.data.upt.alamat1 || data.data.upt.alamat2 || '') + '" readonly></div>';
            html += '</div>';

            // Anggaran Info (Readonly) - Get from form data
            $.get('{{ route("anggaran.entry-anggaran-internal.form") }}', function(formData) {
                html += '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
                html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anggaran</label><input type="text" id="anggaran" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + new Intl.NumberFormat('id-ID', {
                    style: 'currency'
                    , currency: 'IDR'
                    , minimumFractionDigits: 0
                }).format(formData.data.anggaran) + '" readonly></div>';
                html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perubahan UPT</label><input type="text" id="perubahan_upt" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="Rp. 0" readonly></div>';
                html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sisa Pagu</label><input type="text" id="sisa_pagu" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" value="' + new Intl.NumberFormat('id-ID', {
                    style: 'currency'
                    , currency: 'IDR'
                    , minimumFractionDigits: 0
                }).format(formData.data.anggaran) + '" readonly></div>';
                html += '</div>';

                // Form Input with existing data
                html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
             html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Transaksi <span class="text-red-500">*</span></label><input type="date" id="tanggal_trans" name="tanggal_trans" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="' + (data.data.tanggal_trans ? data.data.tanggal_trans.split('T')[0] : '') + '" required></div>';

  html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nominal Perubahan <span class="text-red-500">*</span></label><input type="text" id="nominal" name="nominal" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white currency-input" value="' + new Intl.NumberFormat('id-ID').format(data.data.nominal) + '" required></div>';
                html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Surat <span class="text-red-500">*</span></label><input type="text" id="nomor_surat" name="nomor_surat" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="' + data.data.nomor_surat + '" required></div>';
                html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan <span class="text-red-500">*</span></label><input type="text" id="keterangan" name="keterangan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="' + data.data.keterangan + '" required></div>';
                html += '</div>';

                html += '</div>';

                $('#formBody').html(html);
                $('#formModal').removeClass('hidden');

                // Setup date pickers
                setupDatePickers();

                // Currency formatting
                $('.currency-input').on('input', function() {
                    let value = $(this).val().replace(/[^\d]/g, '');
                    if (value) {
                        $(this).val(new Intl.NumberFormat('id-ID').format(value));
                        calculateSisaPagu();
                    }
                });
            });
        }).fail(function(xhr, status, error) {
            console.error('Error loading edit form data:', error);
            $('#formBody').html('<div class="text-center py-8 text-red-500">Gagal memuat form edit</div>');
            toastr.error('Gagal memuat form edit');
        });
    }

    function calculateSisaPagu() {
        const anggaran = $('#anggaran').val() || 0;
        const nominal = $('#nominal').val().replace(/[^\d]/g, '') || 0;
        const sisaPagu = anggaran - parseInt(nominal);
        $('#sisa_pagu').val(new Intl.NumberFormat('id-ID', {
            style: 'currency'
            , currency: 'IDR'
            , minimumFractionDigits: 0
        }).format(sisaPagu));
    }

    function saveAnggaran() {
        const formData = {
            tanggal_trans: $('#tanggal_trans').val()
            , nominal: $('#nominal').val().replace(/[^\d]/g, '')
            , nomor_surat: $('#nomor_surat').val()
            , keterangan: $('#keterangan').val()
        };

        if (currentEditId) {
            formData.id = currentEditId;
        }

        const url = currentEditId ? '{{ route("anggaran.entry-anggaran-internal.update") }}' : '{{ route("anggaran.entry-anggaran-internal.create") }}';

        $.ajax({
            url: url
            , type: 'POST'
            , data: formData
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#formModal').addClass('hidden');
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

    function viewAnggaran(id) {
        $('#viewModal').removeClass('hidden');
        $('#viewModalBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.entry-anggaran-internal.view", [":id"]) }}'.replace(':id', id), function(data) {
            const item = data.data;
            const tanggalTrans = new Date(item.tanggal_trans).toLocaleDateString('id-ID');
            const nominal = new Intl.NumberFormat('id-ID', {
                style: 'currency'
                , currency: 'IDR'
                , minimumFractionDigits: 0
            }).format(item.nominal);

            let html = '<div class="space-y-4">';
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">UPT</label><p class="text-sm text-gray-900 dark:text-white">' + (item.upt ? item.upt.nama : '-') + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Transaksi</label><p class="text-sm text-gray-900 dark:text-white">' + tanggalTrans + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nominal</label><p class="text-sm text-gray-900 dark:text-white font-bold">' + nominal + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Surat</label><p class="text-sm text-gray-900 dark:text-white">' + item.nomor_surat + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label><p class="text-sm text-gray-900 dark:text-white">' + item.keterangan + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label><p class="text-sm text-gray-900 dark:text-white">' + item.status_perubahan_text + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Input</label><p class="text-sm text-gray-900 dark:text-white">' + (item.user_input ? (item.user_input.nama_lengkap || item.user_input.username) : '-') + '</p></div>';
            if (item.user_app) {
                html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Approve</label><p class="text-sm text-gray-900 dark:text-white">' + (item.user_app.nama_lengkap || item.user_app.username) + '</p></div>';
            }
            html += '</div>';
            html += '</div>';

            $('#viewModalBody').html(html);
        }).fail(function(xhr, status, error) {
            console.error('Error loading view data:', error);
            $('#viewModalBody').html('<div class="text-center py-8 text-red-500">Gagal memuat data detail</div>');
            toastr.error('Gagal memuat data detail');
        });
    }

    function editAnggaran(id) {
        currentEditId = id;
        $('#modalTitle').text('Edit Anggaran Internal');
        loadEditFormData(id);
    }

    function deleteAnggaran(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '{{ route("anggaran.entry-anggaran-internal.delete", [":id"]) }}'.replace(':id', id)
                , type: 'DELETE'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
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

    function closeFormModal() {
        $('#formModal').addClass('hidden');
        currentEditId = null;
    }

    function closeViewModal() {
        $('#viewModal').addClass('hidden');
    }

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

</script>
@endsection
