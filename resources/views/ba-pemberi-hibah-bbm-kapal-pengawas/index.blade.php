@extends('layouts.dashboard')

@section('title', 'BA Pemberi Hibah BBM Kapal Pengawas')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">BA Pemberi Hibah BBM Kapal Pengawas</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola Berita Acara Pemberi Hibah BBM Kapal Pengawas</p>
        </div>
        <button id="createBaBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah BA
        </button>
    </div>

    <!-- Filter and BA Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cari BA/Kapal</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari nomor surat, kapal, lokasi..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kapal Filter -->
                <div class="w-full sm:w-40">
                    <label for="kapal" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Filter Kapal</label>
                    <select id="kapal" name="kapal" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Kapal</option>
                        @foreach($kapals as $kapal)
                        <option value="{{ $kapal->code_kapal }}">{{ $kapal->nama_kapal }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div class="w-full sm:w-40">
                    <label for="date_from" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Dari</label>
                    <input type="date" id="date_from" name="date_from" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Date To -->
                <div class="w-full sm:w-40">
                    <label for="date_to" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Sampai</label>
                    <input type="date" id="date_to" name="date_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Per Page Selector -->
                <div class="w-full sm:w-32">
                    <label for="perPage" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Per Halaman</label>
                    <select id="perPage" name="per_page" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Clear Filter Button -->
                <div class="w-full sm:w-auto">
                    <button type="button" id="clearFilterBtn" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- BA Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nomor Surat & Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kapal Pemberi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kapal Penerima</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Volume Hibah</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="baTableBody" class="bg-white dark:bg-gray-800">
                    <!-- Data akan dimuat via AJAX -->
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
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script>
    $(document).ready(function() {
        // --- State Variables ---
        let currentPage = 1;
        let perPage = 10;
        let currentBaId = null;
        let currentEditMode = false;

        // --- Initialization ---
        loadData();
        setupEventHandlers();
        setDefaultDates();
        setupDatePickers();

        // --- Utility Functions ---
        function formatDateForInput(dateString) {
            if (!dateString) return '';
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                return dateString;
            }
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            } catch (e) {
                console.warn('Error formatting date:', dateString, e);
                return '';
            }
        }

        function formatTimeForInput(timeString) {
            if (!timeString) return '';
            if (/^\d{2}:\d{2}$/.test(timeString)) {
                return timeString;
            }
            if (/^\d{2}:\d{2}:\d{2}$/.test(timeString)) {
                return timeString.substring(0, 5);
            }
            try {
                if (timeString.includes(':')) {
                    const parts = timeString.split(':');
                    if (parts.length >= 2) {
                        const hours = String(parseInt(parts[0])).padStart(2, '0');
                        const minutes = String(parseInt(parts[1])).padStart(2, '0');
                        return `${hours}:${minutes}`;
                    }
                }
                return '';
            } catch (e) {
                console.warn('Error formatting time:', timeString, e);
                return '';
            }
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit'
                , month: '2-digit'
                , year: 'numeric'
            });
        }

        function formatNumber(number) {
            if (!number && number !== 0) return '0';
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 2
            }).format(number);
        }

        // --- UI/UX Feedback Functions ---
        function showLoading() {
            $('#baTableBody').html(`
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span>
                        </div>
                    </td>
                </tr>
            `);
        }

        function hideLoading() {}

        function showSuccess(message) {
            if (typeof toastr !== 'undefined') toastr.success(message);
        }

        function showError(message) {
            if (typeof toastr !== 'undefined') toastr.error(message);
        }

        // --- Form and Modal Handlers ---
        function setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            if ($('#tanggal_surat').length) $('#tanggal_surat').val(today);
        }

        function setupDatePickers() {
            $('input[type="date"]').each(function() {
                const $input = $(this);
                $input.on('click', function(e) {
                    e.preventDefault();
                    this.showPicker && this.showPicker();
                });
                $input.on('focus', function() {
                    this.showPicker && this.showPicker();
                });
                $input.css({
                    'cursor': 'pointer'
                    , 'background-color': 'transparent'
                });
            });

            $('input[type="time"]').each(function() {
                const $input = $(this);
                $input.on('click', function(e) {
                    e.preventDefault();
                    this.showPicker && this.showPicker();
                });
                $input.on('focus', function() {
                    this.showPicker && this.showPicker();
                });
                $input.css({
                    'cursor': 'pointer'
                    , 'background-color': 'transparent'
                });
            });
        }

        function resetForm() {
            $('#baForm')[0].reset();
            $('#modalTitle').text('Form Tambah BA Pemberi Hibah BBM Kapal Pengawas');
            $('#submitBtn').html('Simpan BA');
            currentBaId = null;
            currentEditMode = false;
            clearKapalData();
            clearKapalPenerimaData();
            setDefaultDates();
            $('#an_staf, #an_nakhoda, #an_kkm, #an_nakhoda_penerima, #an_kkm_penerima').prop('checked', false);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        function displayValidationErrors(errors) {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            Object.keys(errors).forEach(field => {
                const fieldName = field.includes('.') ? field.split('.')[0] : field;
                const input = $(`[name="${fieldName}"]`);
                input.addClass('is-invalid');
                const errorMessage = errors[field][0];
                if (input.parent().hasClass('input-group')) {
                    input.parent().after(`<div class="invalid-feedback">${errorMessage}</div>`);
                } else {
                    input.after(`<div class="invalid-feedback">${errorMessage}</div>`);
                }
            });
        }

        // --- Data Handlers ---
        function loadData() {
            const params = {
                page: currentPage
                , per_page: perPage
                , search: $('#search').val()
                , kapal: $('#kapal').val()
                , date_from: $('#date_from').val()
                , date_to: $('#date_to').val()
            };

            $.ajax({
                url: '{{ route("ba-pemberi-hibah-bbm-kapal-pengawas.data") }}'
                , type: 'GET'
                , data: params
                , beforeSend: function() {
                    showLoading();
                }
                , success: function(response) {
                    if (response.success) {
                        renderTable(response.data);
                        renderPagination(response.pagination);
                    } else {
                        showError('Gagal memuat data: ' + response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Terjadi kesalahan saat memuat data');
                    console.error('AJAX Error loadData:', xhr);
                }
                , complete: function() {
                    hideLoading();
                }
            });
        }

        function loadKapalData(kapalId) {
            $.ajax({
                url: '{{ route("ba-pemberi-hibah-bbm-kapal-pengawas.kapal-data") }}'
                , type: 'GET'
                , data: {
                    kapal_id: kapalId
                }
                , success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#code_kapal').val(data.code_kapal);
                        $('#alamat_upt').val(data.alamat_upt);
                        $('#zona_waktu_surat').val(data.zona_waktu_upt);
                        $('#nama_nahkoda').val(data.nama_nakoda);
                        $('#nip_nahkoda').val(data.nip_nakoda);
                        $('#nama_kkm').val(data.nama_kkm);
                        $('#nip_kkm').val(data.nip_kkm);
                    } else {
                        clearKapalData();
                    }
                }
                , error: function(xhr) {
                    console.error('AJAX Error loadKapalData:', xhr);
                }
            });
        }

        function loadKapalPenerimaData(kapalId) {
            $.ajax({
                url: '{{ route("ba-pemberi-hibah-bbm-kapal-pengawas.kapal-penerima-data") }}'
                , type: 'GET'
                , data: {
                    kapal_id: kapalId
                }
                , success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#code_kapal_penerima').val(data.code_kapal);
                        $('#nama_nahkoda_penerima').val(data.nama_nakoda);
                        $('#nip_nahkoda_penerima').val(data.nip_nakoda);
                        $('#nama_kkm_penerima').val(data.nama_kkm);
                        $('#nip_kkm_penerima').val(data.nip_kkm);
                    } else {
                        clearKapalPenerimaData();
                    }
                }
                , error: function(xhr) {
                    console.error('AJAX Error loadKapalPenerimaData:', xhr);
                }
            });
        }

        function loadPersetujuanData(persetujuanId) {
            $.ajax({
                url: '{{ route("ba-pemberi-hibah-bbm-kapal-pengawas.persetujuan-data") }}'
                , type: 'GET'
                , data: {
                    persetujuan_id: persetujuanId
                }
                , success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#nomor_persetujuan').val(data.nomor_persetujuan);
                        $('#tanggal_persetujuan').val(formatDateForInput(data.tanggal_persetujuan));
                    }
                }
                , error: function(xhr) {
                    console.error('AJAX Error loadPersetujuanData:', xhr);
                }
            });
        }

        function clearKapalData() {
            $('#code_kapal').val('');
            $('#alamat_upt').val('');
            $('#nama_staf_pangkalan').val('');
            $('#nip_staf').val('');
            $('#jabatan_staf_pangkalan').val('');
            $('#nama_nahkoda').val('');
            $('#pangkat_nahkoda').val('');
            $('#nip_nahkoda').val('');
            $('#nama_kkm').val('');
            $('#nip_kkm').val('');
        }

        function clearKapalPenerimaData() {
            $('#code_kapal_penerima').val('');
            $('#nama_nahkoda_penerima').val('');
            $('#pangkat_nahkoda_penerima').val('');
            $('#nip_nahkoda_penerima').val('');
            $('#nama_kkm_penerima').val('');
            $('#nip_kkm_penerima').val('');
        }

        // --- Render Functions ---
        function renderTable(data) {
            const tbody = $('#baTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                            <div class="flex flex-col items-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data BA</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan BA pertama</p>
                                <button id="createFirstBaBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah BA Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            data.forEach((ba, index) => {
                const row = `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">
                                <div class="font-medium">${ba.nomor_surat}</div>
                                <div class="text-gray-500 dark:text-gray-400">${formatDate(ba.tanggal_surat)}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">
                                <div class="font-medium">${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</div>
                                <div class="text-gray-500 dark:text-gray-400">${ba.lokasi_surat || '-'}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">
                                <div class="font-medium">${ba.kapal_code_temp || '-'}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">
                                <div class="font-medium">${formatNumber(ba.volume_pengisian)} Liter</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                            <div class="flex items-center space-x-2 justify-end">
                                <button onclick="viewBa(${ba.trans_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button onclick="editBa(${ba.trans_id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                ${ba.file_upload ? `
                                <!-- Button Lihat Dokumen - tampil jika ada file -->
                                <button onclick="viewDocument(${ba.trans_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </button>
                                <!-- Button Ganti Dokumen - hidden untuk sementara -->
                                <button onclick="openUploadModal(${ba.trans_id})" class="p-2 text-orange-600 bg-orange-50 hover:bg-orange-100 border border-orange-200 hover:border-orange-300 dark:bg-orange-900/30 dark:text-orange-400 dark:hover:bg-orange-900/50 dark:border-orange-700 dark:hover:border-orange-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md hidden" title="Ganti Dokumen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                </button>
                                ` : `
                                <!-- Button Upload Dokumen - tampil jika belum ada file -->
                                <button onclick="openUploadModal(${ba.trans_id})" class="p-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Upload Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </button>
                                `}
                                <button onclick="generatePdf(${ba.trans_id})" class="p-2 text-purple-600 bg-purple-50 hover:bg-purple-100 border border-purple-200 hover:border-purple-300 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50 dark:border-purple-700 dark:hover:border-purple-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Generate PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteBa(${ba.trans_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus">
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

        function renderPagination(pagination) {
            const container = $('#paginationContainer');
            container.empty();

            if (pagination.last_page <= 1) return;

            let paginationHtml = '<div class="flex items-center justify-between mt-4">';
            paginationHtml += '<div class="text-sm text-gray-700 dark:text-gray-300">';
            paginationHtml += `Menampilkan ${pagination.from || 0} sampai ${pagination.to || 0} dari ${pagination.total} data`;
            paginationHtml += '</div>';
            paginationHtml += '<div class="flex space-x-1">';

            // Previous button
            if (pagination.current_page > 1) {
                paginationHtml += `<button onclick="changePage(${pagination.current_page - 1})" class="px-3 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600">Previous</button>`;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === pagination.current_page) {
                    paginationHtml += `<button class="px-3 py-1 text-sm bg-blue-600 text-white border border-blue-600 rounded">${i}</button>`;
                } else if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    paginationHtml += `<button onclick="changePage(${i})" class="px-3 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600">${i}</button>`;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    paginationHtml += '<span class="px-2">...</span>';
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                paginationHtml += `<button onclick="changePage(${pagination.current_page + 1})" class="px-3 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600">Next</button>`;
            }

            paginationHtml += '</div></div>';
            container.html(paginationHtml);
        }

        // --- Event Handlers ---
        function setupEventHandlers() {
            // Create BA button
            $('#createBaBtn, #createFirstBaBtn').on('click', function() {
                resetForm();
                $('#baModal').removeClass('hidden');
            });

            // Close modal buttons
            $('#closeModal, #cancelBtn').on('click', function() {
                $('#baModal').addClass('hidden');
            });

            // Close view modal buttons
            $('#closeViewModal, #closeViewModalBtn').on('click', function() {
                $('#viewBaModal').addClass('hidden');
            });

            // Close upload modal buttons
            $('#closeUploadModal, #cancelUploadBtn').on('click', function() {
                $('#uploadModal').addClass('hidden');
            });

            // Close view document modal
            $('#closeViewDocumentModal').on('click', function() {
                $('#viewDocumentModal').addClass('hidden');
            });

            // Delete document button
            $('#deleteDocumentBtn').on('click', function() {
                if (currentBaId) {
                    deleteDocument(currentBaId);
                }
            });

            // Kapal change
            $('#kapal_id').on('change', function() {
                const kapalId = $(this).val();
                if (kapalId) {
                    loadKapalData(kapalId);
                } else {
                    clearKapalData();
                }
            });

            // Kapal Penerima change
            $('#kapal_penerima_id').on('change', function() {
                const kapalId = $(this).val();
                if (kapalId) {
                    loadKapalPenerimaData(kapalId);
                } else {
                    clearKapalPenerimaData();
                }
            });

            // Persetujuan change
            $('#persetujuan_id').on('change', function() {
                const persetujuanId = $(this).val();
                if (persetujuanId) {
                    loadPersetujuanData(persetujuanId);
                }
            });

            // Form submit
            $('#baForm').on('submit', function(e) {
                e.preventDefault();
                saveBa();
            });

            // Upload form submit
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                uploadDocument();
            });

            // Filter form changes
            $('#search').on('input', debounce(function() {
                currentPage = 1;
                loadData();
            }, 500));

            $('#kapal, #date_from, #date_to, #perPage').on('change', function() {
                currentPage = 1;
                perPage = $('#perPage').val();
                loadData();
            });

            // Clear filter button
            $('#clearFilterBtn').on('click', function() {
                $('#filterForm')[0].reset();
                currentPage = 1;
                perPage = 10;
                loadData();
            });

            // Event delegation for dynamically created first BA button
            $(document).on('click', '#createFirstBaBtn', function() {
                resetForm();
                $('#baModal').removeClass('hidden');
            });
        }

        // --- CRUD Operations ---
        function saveBa() {
            const formData = new FormData($('#baForm')[0]);
            const url = currentEditMode ? `/ba-pemberi-hibah-bbm-kapal-pengawas/${currentBaId}` : '{{ route("ba-pemberi-hibah-bbm-kapal-pengawas.store") }}';
            const method = currentEditMode ? 'POST' : 'POST';

            if (currentEditMode) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url
                , type: method
                , data: formData
                , processData: false
                , contentType: false
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#baModal').addClass('hidden');
                        loadData();
                        resetForm();
                    } else {
                        showError(response.message);
                        if (response.errors) {
                            displayValidationErrors(response.errors);
                        }
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        displayValidationErrors(errors);
                        showError('Validasi gagal. Periksa input Anda.');
                    } else {
                        showError('Gagal menyimpan BA');
                    }
                    console.error('AJAX Error saveBa:', xhr);
                }
            });
        }

        window.viewBa = function(id) {
            $.ajax({
                url: `/ba-pemberi-hibah-bbm-kapal-pengawas/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        const ba = response.data;
                        let html = '<div class="space-y-4">';

                        html += '<div class="grid grid-cols-2 gap-4">';
                        html += `<div><strong>Nomor Surat:</strong> ${ba.nomor_surat}</div>`;
                        html += `<div><strong>Tanggal:</strong> ${formatDate(ba.tanggal_surat)}</div>`;
                        html += `<div><strong>Lokasi:</strong> ${ba.lokasi_surat}</div>`;
                        html += `<div><strong>Kapal Pemberi:</strong> ${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</div>`;
                        html += `<div><strong>Kapal Penerima:</strong> ${ba.kapal_code_temp}</div>`;
                        html += `<div><strong>Volume Hibah:</strong> ${formatNumber(ba.volume_pengisian)} Liter</div>`;
                        html += `<div><strong>Jenis BBM:</strong> ${ba.keterangan_jenis_bbm}</div>`;
                        html += `<div><strong>No Persetujuan:</strong> ${ba.link_modul_ba}</div>`;
                        html += '</div>';

                        html += '</div>';
                        $('#viewBaContent').html(html);
                        $('#viewBaModal').removeClass('hidden');
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal memuat data BA');
                    console.error('AJAX Error viewBa:', xhr);
                }
            });
        };

        window.editBa = function(id) {
            currentBaId = id;
            currentEditMode = true;

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-kapal-pengawas/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        const ba = response.data;
                        fillEditForm(ba);
                        $('#modalTitle').text('Form Edit BA Pemberi Hibah BBM Kapal Pengawas');
                        $('#submitBtn').html('Update BA');
                        $('#baModal').removeClass('hidden');
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Terjadi kesalahan saat memuat data BA untuk diedit');
                    console.error('AJAX Error editBa:', xhr);
                }
            });
        };

        function fillEditForm(ba) {
            $('#baId').val(ba.trans_id);
            $('#kapal_id').val(ba.kapal ? ba.kapal.m_kapal_id : '').trigger('change');

            // Kapal Penerima - perlu cari berdasarkan kapal_code_temp
            if (ba.kapal_code_temp) {
                // AJAX call untuk mencari kapal berdasarkan code_kapal
                $.ajax({
                    url: '{{ route("ba-pemberi-hibah-bbm-kapal-pengawas.kapal-data") }}'
                    , type: 'GET'
                    , data: {
                        code_kapal: ba.kapal_code_temp
                    }
                    , success: function(response) {
                        if (response.success && response.data.m_kapal_id) {
                            $('#kapal_penerima_id').val(response.data.m_kapal_id).trigger('change');
                        }
                    }
                    , error: function() {
                        // Fallback: cari manual di dropdown
                        const kapalPenerimaOption = $('#kapal_penerima_id option').filter(function() {
                            return $(this).text().includes(ba.kapal_code_temp);
                        }).first();
                        if (kapalPenerimaOption.length) {
                            $('#kapal_penerima_id').val(kapalPenerimaOption.val()).trigger('change');
                        }
                    }
                });
            }

            $('#nomor_surat').val(ba.nomor_surat);
            $('#tanggal_surat').val(formatDateForInput(ba.tanggal_surat));
            $('#jam_surat').val(formatTimeForInput(ba.jam_surat));
            $('#zona_waktu_surat').val(ba.zona_waktu_surat);
            $('#lokasi_surat').val(ba.lokasi_surat);

            // Persetujuan - isi berdasarkan m_persetujuan_id atau deskripsi
            if (ba.m_persetujuan_id) {
                $('#persetujuan_id').val(ba.m_persetujuan_id).trigger('change');
            }
            $('#tanggal_persetujuan').val(formatDateForInput(ba.tgl_persetujuan));

            $('#keterangan_jenis_bbm').val(ba.keterangan_jenis_bbm);
            $('#volume_sebelum').val(ba.volume_sebelum);
            $('#volume_pemakaian').val(ba.volume_pemakaian);
            $('#volume_sisa').val(ba.volume_sisa);
            $('#sebab_temp').val(ba.sebab_temp);
            $('#nama_staf_pangkalan').val(ba.nama_staf_pangkalan);
            $('#nip_staf').val(ba.nip_staf);
            $('#jabatan_staf_pangkalan').val(ba.jabatan_staf_pangkalan);
            $('#nama_nahkoda').val(ba.nama_nahkoda);
            $('#pangkat_nahkoda').val(ba.pangkat_nahkoda);
            $('#nip_nahkoda').val(ba.nip_nahkoda);
            $('#nama_kkm').val(ba.nama_kkm);
            $('#nip_kkm').val(ba.nip_kkm);
            $('#nama_nahkoda_penerima').val(ba.nama_nahkoda_temp);
            $('#pangkat_nahkoda_penerima').val(ba.pangkat_nahkoda_temp);
            $('#nip_nahkoda_penerima').val(ba.nip_nahkoda_temp);
            $('#nama_kkm_penerima').val(ba.nama_kkm_temp);
            $('#nip_kkm_penerima').val(ba.nip_kkm_temp);
            $('#an_staf').prop('checked', ba.an_staf == 1);
            $('#an_nakhoda').prop('checked', ba.an_nakhoda == 1);
            $('#an_kkm').prop('checked', ba.an_kkm == 1);
            $('#an_nakhoda_penerima').prop('checked', ba.an_nakhoda_temp == 1);
            $('#an_kkm_penerima').prop('checked', ba.an_kkm_temp == 1);
        }

        window.deleteBa = function(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus BA ini?')) {
                return;
            }

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-kapal-pengawas/${id}`
                , type: 'POST'
                , data: {
                    _method: 'DELETE'
                }
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        loadData();
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal menghapus BA');
                    console.error('AJAX Error deleteBa:', xhr);
                }
            });
        };

        window.generatePdf = function(baId) {
            showSuccess('Sedang membuat PDF...');

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-kapal-pengawas/${baId}/pdf`
                , type: 'GET'
                , timeout: 30000
                , success: function(response) {
                    if (response.success) {
                        window.open(response.download_url, '_blank');
                        showSuccess('PDF berhasil dibuat dan dibuka di tab baru');
                    } else {
                        showError(response.message || 'Gagal membuat PDF');
                    }
                }
                , error: function(xhr) {
                    showError('Gagal membuat PDF');
                    console.error('AJAX Error generatePdf:', xhr);
                }
            });
        };

        window.openUploadModal = function(baId) {
            currentBaId = baId;
            $('#uploadModal').removeClass('hidden');
            $('#uploadForm')[0].reset();
        };

        function uploadDocument() {
            const formData = new FormData($('#uploadForm')[0]);

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-kapal-pengawas/${currentBaId}/upload`
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#uploadModal').addClass('hidden');
                        loadData();
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal upload dokumen');
                    console.error('AJAX Error uploadDocument:', xhr);
                }
            });
        }

        function deleteDocument(baId) {
            if (!confirm('Apakah Anda yakin ingin menghapus dokumen pendukung ini?')) {
                return;
            }

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-kapal-pengawas/${baId}/delete-document`
                , type: 'DELETE'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#viewDocumentModal').addClass('hidden');
                        loadData();
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal menghapus dokumen');
                    console.error('AJAX Error deleteDocument:', xhr);
                }
            });
        }

        window.viewDocument = function(baId) {
            currentBaId = baId;

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-kapal-pengawas/${baId}/view-document`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        $('#documentViewer').html(`
                            <div class="text-center">
                                <div class="mb-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">${response.filename}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Dokumen pendukung BA</p>
                                </div>
                                <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                    <iframe src="${response.file_url}" width="100%" height="600px" class="border-0 rounded-lg"></iframe>
                                </div>
                                <div class="mt-4">
                                    <a href="${response.file_url}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Buka di Tab Baru
                                    </a>
                                </div>
                            </div>
                        `);
                        $('#viewDocumentModal').removeClass('hidden');
                        showSuccess('Dokumen berhasil dimuat');
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal memuat dokumen');
                    console.error('AJAX Error viewDocument:', xhr);
                }
            });
        };

        window.changePage = function(page) {
            currentPage = page;
            loadData();
        };
    });

</script>
@endsection

@section('modals')
<!-- BA Modal -->
<div id="baModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-white">Form Tambah BA Pemberi Hibah BBM Kapal Pengawas</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data berikut untuk membuat Berita Acara</p>
                    </div>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="baForm" class="mt-6 space-y-6">
                    @csrf
                    <input type="hidden" id="baId" name="ba_id">

                    <!-- Informasi Kapal Pemberi -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kapal Pemberi Hibah
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="kapal_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Pilih Kapal Pemberi <span class="text-red-500">*</span>
                                </label>
                                <select id="kapal_id" name="kapal_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">-- Pilih Kapal --</option>
                                    @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->m_kapal_id }}">{{ $kapal->nama_kapal }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="code_kapal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kode Kapal</label>
                                <input type="text" id="code_kapal" name="code_kapal" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Lokasi dan Waktu -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-green-900 dark:text-green-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Lokasi dan Waktu
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="alamat_upt" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alamat UPT</label>
                                <textarea id="alamat_upt" name="alamat_upt" rows="3" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed resize-none"></textarea>
                            </div>
                            <div>
                                <label for="lokasi_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Lokasi Pembuatan BA <span class="text-red-500">*</span>
                                </label>
                                <textarea id="lokasi_surat" name="lokasi_surat" rows="3" required placeholder="Masukkan lokasi pembuatan BA..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors resize-none"></textarea>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="lg:col-span-2">
                                <label for="nomor_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nomor BA <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nomor_surat" name="nomor_surat" required placeholder="BA/001/KKP/2024" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="tanggal_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tanggal_surat" name="tanggal_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="jam_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jam <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="jam_surat" name="jam_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="zona_waktu_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Zona Waktu <span class="text-red-500">*</span>
                            </label>
                            <select id="zona_waktu_surat" name="zona_waktu_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="WIB">WIB (Waktu Indonesia Barat)</option>
                                <option value="WITA">WITA (Waktu Indonesia Tengah)</option>
                                <option value="WIT">WIT (Waktu Indonesia Timur)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kapal Penerima Hibah -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kapal Penerima Hibah
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="kapal_penerima_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Pilih Kapal Penerima <span class="text-red-500">*</span>
                                </label>
                                <select id="kapal_penerima_id" name="kapal_penerima_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">-- Pilih Kapal --</option>
                                    @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->m_kapal_id }}">{{ $kapal->nama_kapal }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="code_kapal_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kode Kapal Penerima</label>
                                <input type="text" id="code_kapal_penerima" name="code_kapal_penerima" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Persetujuan -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-orange-900 dark:text-orange-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Berdasarkan Persetujuan
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="persetujuan_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Pilih Persetujuan <span class="text-red-500">*</span>
                                </label>
                                <select id="persetujuan_id" name="persetujuan_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">-- Pilih Persetujuan --</option>
                                    @foreach($persetujuans as $persetujuan)
                                    <option value="{{ $persetujuan->id }}">{{ $persetujuan->deskripsi_persetujuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nomor_persetujuan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nomor Persetujuan</label>
                                <input type="text" id="nomor_persetujuan" name="nomor_persetujuan" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div>
                                <label for="tanggal_persetujuan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tanggal Persetujuan</label>
                                <input type="date" id="tanggal_persetujuan" name="tanggal_persetujuan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Volume Hibah -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-yellow-900 dark:text-yellow-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                            </svg>
                            Informasi BBM Hibah
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="keterangan_jenis_bbm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jenis BBM <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" value="BIO SOLAR" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="volume_sebelum" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">BBM Sebelum Pengisian</label>
                                <input type="number" id="volume_sebelum" name="volume_sebelum" step="0.01" min="0" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="volume_pemakaian" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jumlah BBM Di Hibahkan <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="volume_pemakaian" name="volume_pemakaian" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="volume_sisa" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sisa BBM</label>
                                <input type="number" id="volume_sisa" name="volume_sisa" step="0.01" min="0" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                        <div>
                            <label for="sebab_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Alasan Hibah BBM <span class="text-red-500">*</span>
                            </label>
                            <textarea id="sebab_temp" name="sebab_temp" rows="3" required placeholder="Masukkan alasan hibah BBM..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Staf Pangkalan -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Staf Pangkalan
                        </h4>
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_staf" name="an_staf" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="an_staf" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">An. Staf</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="nama_staf_pangkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Staf Pangkalan</label>
                                <input type="text" id="nama_staf_pangkalan" name="nama_staf_pangkalan" placeholder="Nama lengkap staf pangkalan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_staf" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP Staf</label>
                                <input type="text" id="nip_staf" name="nip_staf" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="jabatan_staf_pangkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jabatan Staf</label>
                                <input type="text" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" placeholder="Jabatan staf pangkalan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Nakhoda & KKM Kapal Pemberi -->
                    <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-cyan-900 dark:text-cyan-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Nakhoda & KKM Kapal Pemberi
                        </h4>
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_nakhoda" name="an_nakhoda" value="1" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                                <label for="an_nakhoda" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">An. Nakhoda</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="nama_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Nakhoda</label>
                                <input type="text" id="nama_nahkoda" name="nama_nahkoda" placeholder="Nama lengkap nakhoda" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="pangkat_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pangkat/Gol</label>
                                <input type="text" id="pangkat_nahkoda" name="pangkat_nahkoda" placeholder="Pangkat/Golongan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_nahkoda" name="nip_nahkoda" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_kkm" name="an_kkm" value="1" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                                <label for="an_kkm" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">An. KKM</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama KKM</label>
                                <input type="text" id="nama_kkm" name="nama_kkm" placeholder="Nama lengkap KKM" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_kkm" name="nip_kkm" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Nakhoda & KKM Kapal Penerima -->
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-red-900 dark:text-red-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Nakhoda & KKM Kapal Penerima
                        </h4>
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_nakhoda_penerima" name="an_nakhoda_penerima" value="1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="an_nakhoda_penerima" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">An. Nakhoda</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="nama_nahkoda_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Nakhoda</label>
                                <input type="text" id="nama_nahkoda_penerima" name="nama_nahkoda_penerima" placeholder="Nama lengkap nakhoda" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="pangkat_nahkoda_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pangkat/Gol</label>
                                <input type="text" id="pangkat_nahkoda_penerima" name="pangkat_nahkoda_penerima" placeholder="Pangkat/Golongan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_nahkoda_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_nahkoda_penerima" name="nip_nahkoda_penerima" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_kkm_penerima" name="an_kkm_penerima" value="1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="an_kkm_penerima" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">An. KKM</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kkm_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama KKM</label>
                                <input type="text" id="nama_kkm_penerima" name="nama_kkm_penerima" placeholder="Nama lengkap KKM" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_kkm_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_kkm_penerima" name="nip_kkm_penerima" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="cancelBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" form="baForm" id="submitBtn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-lg transition-colors">
                        Simpan BA
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Detail -->
<div id="viewBaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail BA Pemberi Hibah BBM Kapal Pengawas</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap Berita Acara</p>
                    </div>
                    <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="viewBaContent" class="mt-6">
                    <!-- Content will be loaded here -->
                </div>
                <div class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="closeViewModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Dokumen -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[99999] hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 id="uploadModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Upload Dokumen Pendukung</h3>
            <button id="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="file" id="documentFile" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900/30 dark:file:text-green-400 dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG (Maksimal 10MB)
                    </p>
                </div>
            </div>
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="cancelUploadBtn" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors font-medium">
                    Batal
                </button>
                <button type="submit" id="uploadBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span id="uploadBtnText">Upload</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal View Dokumen -->
<div id="viewDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[99999] hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dokumen Pendukung</h3>
            <div class="flex items-center space-x-2">
                <button id="deleteDocumentBtn" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200" title="Hapus Dokumen">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
                <button id="closeViewDocumentModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-auto max-h-[calc(90vh-120px)]">
            <div id="documentViewer" class="w-full h-full">
                <!-- Document content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection
