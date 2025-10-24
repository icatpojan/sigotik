@extends('layouts.dashboard')

@section('title', 'Tanggal SPPD')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tanggal SPPD</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola tanggal SPPD untuk tagihan BBM</p>
        </div>
    </div>

    <!-- Filter and Data Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cari No Tagihan/Penyedia</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari no tagihan, penyedia..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kode UPT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tgl Tagihan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No Tagihan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Penyedia</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Quantity (Liter)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Total (Rp)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tanggal SPPD</th>
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

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Tagihan</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lihat detail tagihan BBM</p>
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

<!-- Input Tanggal SPPD Modal -->
<div id="inputTanggalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                            <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Input Tanggal SPPD</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Masukkan tanggal SPPD untuk tagihan</p>
                            </div>
                    <button onclick="closeInputTanggalModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="inputTanggalForm">
                    <div class="mt-6" id="inputTanggalModalBody">
                        <!-- Content akan diisi via JavaScript -->
                    </div>
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 mt-6 space-x-3">
                        <button type="button" onclick="closeInputTanggalModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
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

<!-- Upload File Modal -->
<div id="uploadFileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Upload File SPPD</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload file SPPD untuk tagihan</p>
                    </div>
                    <button onclick="closeUploadFileModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="uploadFileForm" enctype="multipart/form-data">
                    <div class="mt-6" id="uploadFileModalBody">
                        <!-- Content akan diisi via JavaScript -->
                    </div>
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700 mt-6 space-x-3">
                        <button type="button" onclick="closeUploadFileModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                            Upload
                        </button>
                    </div>
                </form>
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

        // Form submissions
        $('#inputTanggalForm').on('submit', function(e) {
                e.preventDefault();
            saveTanggalSppd();
        });

        $('#uploadFileForm').on('submit', function(e) {
                    e.preventDefault();
            uploadFileSppd();
        });
    });

    function loadData() {
        $('#loadingIndicator').removeClass('hidden');

        // Get filter parameters
        const search = $('#search').val();

        $.get('{{ route("anggaran.tanggal-sppd.data") }}', {
            search: search
        }, function(response) {
            $('#loadingIndicator').addClass('hidden');

            if (!response.data || response.data.length === 0) {
                $('#dataTableBody').html(`
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data tagihan
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            response.data.forEach(function(item, index) {
                const tanggalTagihan = new Date(item.tanggal_invoice).toLocaleDateString('id-ID');
                const tanggalSppd = item.tanggal_sppd ? new Date(item.tanggal_sppd).toLocaleDateString('id-ID') : '-';
                const quantity = new Intl.NumberFormat('id-ID').format(item.quantity);
                const total = 'Rp. ' + new Intl.NumberFormat('id-ID').format(item.total);

                let actions = '<div class="flex items-center justify-end space-x-1">';
                actions += '<button onclick="viewTagihan(' + item.tagihan_id + ')" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>';

                if (!item.tanggal_sppd) {
                    actions += '<button onclick="inputTanggalSppd(' + item.tagihan_id + ')" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Input Tanggal SPPD"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></button>';
                }

                actions += '<button onclick="uploadFileSppd(' + item.tagihan_id + ')" class="p-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Upload File SPPD"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg></button>';

                if (item.file_sppd) {
                    actions += '<button onclick="downloadFile(\'' + item.file_sppd + '\')" class="p-2 text-purple-600 bg-purple-50 hover:bg-purple-100 border border-purple-200 hover:border-purple-300 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50 dark:border-purple-700 dark:hover:border-purple-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Download File"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></button>';
                }

                actions += '</div>';

                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${item.m_upt_code}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${tanggalTagihan}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.no_tagihan}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.penyedia}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${quantity}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${total}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${tanggalSppd}</div>
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

    function viewTagihan(id) {
        $('#viewModal').removeClass('hidden');
        $('#viewModalBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.tanggal-sppd.view", [":id"]) }}'.replace(':id', id), function(data) {
            const item = data.data;
            const tanggalTagihan = new Date(item.tanggal_invoice).toLocaleDateString('id-ID');
            const tanggalSppd = item.tanggal_sppd ? new Date(item.tanggal_sppd).toLocaleDateString('id-ID') : '-';
            const quantity = new Intl.NumberFormat('id-ID').format(item.quantity);
            const total = 'Rp. ' + new Intl.NumberFormat('id-ID').format(item.total);
            const hargaperliter = 'Rp. ' + new Intl.NumberFormat('id-ID').format(item.hargaperliter);

            let html = '<div class="space-y-4">';
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No Tagihan</label><p class="text-sm text-gray-900 dark:text-white">' + item.no_tagihan + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Tagihan</label><p class="text-sm text-gray-900 dark:text-white">' + tanggalTagihan + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penyedia</label><p class="text-sm text-gray-900 dark:text-white">' + item.penyedia + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">UPT</label><p class="text-sm text-gray-900 dark:text-white">' + (item.upt ? item.upt.nama : '-') + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label><p class="text-sm text-gray-900 dark:text-white">' + quantity + ' Liter</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga per Liter</label><p class="text-sm text-gray-900 dark:text-white">' + hargaperliter + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label><p class="text-sm text-gray-900 dark:text-white font-bold">' + total + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal SPPD</label><p class="text-sm text-gray-900 dark:text-white">' + tanggalSppd + '</p></div>';
            html += '</div>';
            html += '</div>';

            $('#viewModalBody').html(html);
        }).fail(function(xhr, status, error) {
            console.error('Error loading view data:', error);
            $('#viewModalBody').html('<div class="text-center py-8 text-red-500">Gagal memuat data detail</div>');
            toastr.error('Gagal memuat data detail');
        });
    }

    function inputTanggalSppd(id) {
        $('#inputTanggalModal').removeClass('hidden');
        $('#inputTanggalModalBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.tanggal-sppd.form-input", [":id"]) }}'.replace(':id', id), function(data) {
            const item = data.data;
            let html = '<div class="space-y-4">';
            html += '<input type="hidden" id="tagihan_id" name="tagihan_id" value="' + item.tagihan_id + '">';
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No Tagihan</label><p class="text-sm text-gray-900 dark:text-white">' + item.no_tagihan + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penyedia</label><p class="text-sm text-gray-900 dark:text-white">' + item.penyedia + '</p></div>';
            html += '</div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal SPPD <span class="text-red-500">*</span></label>';
            html += '<input type="date" id="tanggal_sppd" name="tanggal_sppd" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>';
            html += '</div>';
            html += '</div>';

            $('#inputTanggalModalBody').html(html);
        }).fail(function(xhr, status, error) {
            console.error('Error loading form data:', error);
            $('#inputTanggalModalBody').html('<div class="text-center py-8 text-red-500">Gagal memuat form</div>');
            toastr.error('Gagal memuat form');
        });
    }

    function uploadFileSppd(id) {
        $('#uploadFileModal').removeClass('hidden');
        $('#uploadFileModalBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.tanggal-sppd.form-upload", [":id"]) }}'.replace(':id', id), function(data) {
            const item = data.data;
            let html = '<div class="space-y-4">';
            html += '<input type="hidden" id="tagihan_id_upload" name="tagihan_id" value="' + item.tagihan_id + '">';
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No Tagihan</label><p class="text-sm text-gray-900 dark:text-white">' + item.no_tagihan + '</p></div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penyedia</label><p class="text-sm text-gray-900 dark:text-white">' + item.penyedia + '</p></div>';
            html += '</div>';
            html += '<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File SPPD <span class="text-red-500">*</span></label>';
            html += '<input type="file" id="file_sppd" name="file_sppd" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>';
            html += '<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format yang diperbolehkan: PDF, JPG, JPEG, PNG (Maksimal 2MB)</p>';
            html += '</div>';
            html += '</div>';

            $('#uploadFileModalBody').html(html);
        }).fail(function(xhr, status, error) {
            console.error('Error loading upload form:', error);
            $('#uploadFileModalBody').html('<div class="text-center py-8 text-red-500">Gagal memuat form upload</div>');
            toastr.error('Gagal memuat form upload');
        });
    }

    function saveTanggalSppd() {
        const data = {
            id: $('#tagihan_id').val()
            , tanggal_sppd: $('#tanggal_sppd').val()
        };

        $.ajax({
            url: '{{ route("anggaran.tanggal-sppd.update-tanggal") }}'
            , type: 'POST'
            , data: data
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#inputTanggalModal').addClass('hidden');
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

    function uploadFileSppd() {
        const formData = new FormData();
        formData.append('tagihan_id', $('#tagihan_id_upload').val());
        formData.append('file_sppd', $('#file_sppd')[0].files[0]);

            $.ajax({
            url: '{{ route("anggaran.tanggal-sppd.upload-file") }}'
            , type: 'POST'
            , data: formData
            , processData: false
            , contentType: false
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                    toastr.success(response.message);
                    $('#uploadFileModal').addClass('hidden');
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

    function downloadFile(filename) {
        window.open('{{ route("anggaran.tanggal-sppd.download-file", [":filename"]) }}'.replace(':filename', filename), '_blank');
    }

    function closeViewModal() {
        $('#viewModal').addClass('hidden');
    }

    function closeInputTanggalModal() {
        $('#inputTanggalModal').addClass('hidden');
    }

    function closeUploadFileModal() {
        $('#uploadFileModal').addClass('hidden');
    }

</script>
@endsection
