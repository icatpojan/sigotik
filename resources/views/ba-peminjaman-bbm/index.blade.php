@extends('layouts.dashboard')

@section('title', 'BA Peminjaman BBM')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">BA Peminjaman BBM</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola Berita Acara Peminjaman BBM</p>
        </div>
        <div class="flex gap-2">
            <button id="helpBtn" class="inline-flex items-center px-4 py-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </button>
            <button id="createBaBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah BA
            </button>
        </div>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kapal & Lokasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Volume Penggunaan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status</th>
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

        // --- Utility Functions (Dipindahkan ke atas untuk akses mudah) ---

        /**
         * Format tanggal untuk input date HTML5 (YYYY-MM-DD)
         * @param {string} dateString Tanggal dari database
         * @returns {string} Tanggal dalam format YYYY-MM-DD
         */
        function formatDateForInput(dateString) {
            if (!dateString) return '';

            // Jika sudah dalam format YYYY-MM-DD, return langsung
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                return dateString;
            }

            // Jika dalam format lain, coba parse dan format ulang
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

        /**
         * Format waktu untuk input time HTML5 (HH:MM)
         * @param {string} timeString Waktu dari database
         * @returns {string} Waktu dalam format HH:MM
         */
        function formatTimeForInput(timeString) {
            if (!timeString) return '';

            // Jika sudah dalam format HH:MM, return langsung
            if (/^\d{2}:\d{2}$/.test(timeString)) {
                return timeString;
            }

            // Jika dalam format HH:MM:SS, ambil hanya HH:MM
            if (/^\d{2}:\d{2}:\d{2}$/.test(timeString)) {
                return timeString.substring(0, 5);
            }

            // Jika dalam format lain, coba parse dan format ulang
            try {
                // Coba berbagai format waktu
                let time;
                if (timeString.includes(':')) {
                    const parts = timeString.split(':');
                    if (parts.length >= 2) {
                        const hours = String(parseInt(parts[0])).padStart(2, '0');
                        const minutes = String(parseInt(parts[1])).padStart(2, '0');
                        return `${hours}:${minutes}`;
                    }
                }

                // Jika tidak bisa di-parse, return kosong
                return '';
            } catch (e) {
                console.warn('Error formatting time:', timeString, e);
                return '';
            }
        }

        /**
         * Menerapkan fungsi debounce untuk membatasi laju eksekusi.
         * @param {Function} func Fungsi yang akan di-debounce.
         * @param {number} wait Waktu tunggu dalam milidetik.
         */
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

        /**
         * Memformat string tanggal menjadi format lokal (DD/MM/YYYY).
         * @param {string} dateString String tanggal.
         * @returns {string} Tanggal yang diformat atau '-'.
         */
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit'
                , month: '2-digit'
                , year: 'numeric'
            });
        }

        /**
         * Memformat angka dengan pemisah ribuan lokal.
         * @param {number|string} number Angka yang akan diformat.
         * @returns {string} Angka yang diformat atau '0'.
         */
        function formatNumber(number) {
            if (!number && number !== 0) return '0';
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 2
            }).format(number);
        }

        /**
         * Mendapatkan warna status berdasarkan kode.
         * @param {number|string} status Kode status.
         * @returns {string} Nama warna.
         */
        function getStatusColor(status) {
            const colors = {
                0: 'warning', // Input
                1: 'success', // Approval
                2: 'danger' // Batal
            };
            return colors[status] || 'secondary';
        }

        /**
         * Mendapatkan teks status berdasarkan kode.
         * @param {number|string} status Kode status.
         * @returns {string} Teks status.
         */
        function getStatusText(status) {
            const texts = {
                0: 'Input'
                , 1: 'Approval'
                , 2: 'Batal'
            };
            return texts[status] || 'Unknown';
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

        function hideLoading() {
            // Konten akan diganti oleh `renderTable`
        }

        function showSuccess(message) {
            // Asumsi `toastr` tersedia
            if (typeof toastr !== 'undefined') toastr.success(message);
        }

        function showError(message) {
            // Asumsi `toastr` tersedia
            if (typeof toastr !== 'undefined') toastr.error(message);
        }

        // --- Form and Modal Handlers ---

        function setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            const now = new Date();
            const currentTime = now.toTimeString().slice(0, 5); // HH:MM format

            // Pastikan elemen tanggal ada sebelum mengatur nilainya
            if ($('#tanggal_surat').length) $('#tanggal_surat').val(today);
            if ($('#jam_surat').length) $('#jam_surat').val(currentTime);
            if ($('#tanggal_sebelum').length) $('#tanggal_sebelum').val(today);
            if ($('#tanggal_pengisian').length) $('#tanggal_pengisian').val(today);
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

                // Ensure proper styling
                $input.css({
                    'cursor': 'pointer'
                    , 'background-color': 'transparent'
                });
            });

            // Also setup time inputs
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
            $('#modalTitle').text('Form Tambah BA Peminjaman BBM');
            $('#submitBtn').html('<i class="fas fa-save me-2"></i>Simpan BA');
            currentBaId = null;
            currentEditMode = false;
            clearKapalData();
            clearKapalPeminjamData();
            setDefaultDates();

            // Reset checkboxes dan hidden input terkait
            $('#an_staf, #an_nakhoda, #an_kkm, #an_nakhoda_temp, #an_kkm_temp').prop('checked', false).trigger('change');

            // Clear validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        function resetUploadForm() {
            $('#uploadForm')[0].reset();
            $('#uploadProgress').hide();
            $('#uploadProgress .progress-bar').css('width', '0%');
            // currentBaId tidak di-reset di sini agar dapat digunakan untuk upload
        }

        function displayValidationErrors(errors) {
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Display new errors
            Object.keys(errors).forEach(field => {
                // Hilangkan `.` jika ada (misal, untuk array input)
                const fieldName = field.includes('.') ? field.split('.')[0] : field;
                const input = $(`[name="${fieldName}"]`);

                // Cari elemen terdekat yang memiliki kelas form-control atau sejenis
                // atau cukup tambahkan kelas invalid pada input itu sendiri
                input.addClass('is-invalid');

                // Tambahkan pesan error
                const errorMessage = errors[field][0];
                // Tambahkan div feedback, cari parent terdekat untuk penempatan yang rapi
                if (input.parent().hasClass('input-group')) {
                    // Untuk input group, tambahkan setelah parent
                    input.parent().after(`<div class="invalid-feedback">${errorMessage}</div>`);
                } else {
                    // Untuk input biasa, tambahkan setelah input
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
                url: '{{ route("ba-peminjaman-bbm.data") }}'
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

        function loadKapalData(kapalId, preserveUserData = false) {
            $.ajax({
                url: '{{ route("ba-peminjaman-bbm.kapal-data") }}'
                , type: 'GET'
                , data: {
                    kapal_id: kapalId
                }
                , success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#code_kapal').val(data.code_kapal);
                        $('#alamat_upt').val(data.alamat_upt);
                        $('#lokasi_surat').val(data.kota || data.lokasi_surat);
                        $('#zona_waktu_surat').val(data.zona_waktu_upt);

                        // Cek apakah field sudah terisi dari database sebelum auto-fill
                        if (!preserveUserData) {
                            $('#jabatan_staf_pangkalan').val(data.jabatan_petugas);
                            $('#nama_staf_pagkalan').val(data.nama_petugas);
                            $('#nip_staf').val(data.nip_petugas);
                            $('#nama_nahkoda').val(data.nama_nakoda);
                            $('#pangkat_nahkoda').val(data.jabatan_nakhoda);
                            $('#nip_nahkoda').val(data.nip_nakoda);
                            $('#nama_kkm').val(data.nama_kkm);
                            $('#nip_kkm').val(data.nip_kkm);
                        } else {
                            // Hanya isi jika field kosong
                            if (!$('#jabatan_staf_pangkalan').val()) $('#jabatan_staf_pangkalan').val(data.jabatan_petugas);
                            if (!$('#nama_staf_pagkalan').val()) $('#nama_staf_pagkalan').val(data.nama_petugas);
                            if (!$('#nip_staf').val()) $('#nip_staf').val(data.nip_petugas);
                            if (!$('#nama_nahkoda').val()) $('#nama_nahkoda').val(data.nama_nakoda);
                            if (!$('#pangkat_nahkoda').val()) $('#pangkat_nahkoda').val(data.jabatan_nakhoda);
                            if (!$('#nip_nahkoda').val()) $('#nip_nahkoda').val(data.nip_nakoda);
                            if (!$('#nama_kkm').val()) $('#nama_kkm').val(data.nama_kkm);
                            if (!$('#nip_kkm').val()) $('#nip_kkm').val(data.nip_kkm);
                        }
                    } else {
                        clearKapalData();
                    }
                }
                , error: function(xhr) {
                    console.error('AJAX Error loadKapalData:', xhr);
                }
            });
        }

        function loadBaData(kapalId) {
            const tanggalSurat = $('#tanggal_surat').val();
            if (!tanggalSurat) return;

            $.ajax({
                url: '{{ route("ba-peminjaman-bbm.ba-data") }}'
                , type: 'GET'
                , data: {
                    kapal_id: kapalId
                    , tanggal_surat: tanggalSurat
                }
                , success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#link_ba').val(data.link_ba);
                        $('#volume_sebelum').val(data.volume_sisa);
                        $('#keterangan_jenis_bbm').val(data.keterangan_jenis_bbm);
                        calculateVolumeSisa(); // Hitung ulang setelah memuat data BA
                    } else {
                        // Jika tidak ada data BA sebelumnya, biarkan kosong atau atur ke default
                        $('#link_ba').val('');
                        // JANGAN me-reset volume_sebelum jika ini mode EDIT dan kapal tidak berubah
                        if (!currentEditMode) {
                            $('#volume_sebelum').val('0');
                        }
                        $('#keterangan_jenis_bbm').val('');
                    }
                }
                , error: function(xhr) {
                    console.error('AJAX Error loadBaData:', xhr);
                }
            });
        }

        function loadKapalPeminjamData(kapalPeminjamId) {
            $.ajax({
                url: '{{ route("ba-peminjaman-bbm.kapal-data") }}'
                , type: 'GET'
                , data: {
                    kapal_id: kapalPeminjamId
                }
                , success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#nama_nahkoda_temp').val(data.nama_nakoda);
                        $('#pangkat_nahkoda_temp').val(data.jabatan_nakhoda);
                        $('#nip_nahkoda_temp').val(data.nip_nakoda);
                        $('#nama_kkm_temp').val(data.nama_kkm);
                        $('#nip_kkm_temp').val(data.nip_kkm);
                    } else {
                        clearKapalPeminjamData();
                    }
                }
                , error: function(xhr) {
                    console.error('AJAX Error loadKapalPeminjamData:', xhr);
                }
            });
        }

        function clearKapalPeminjamData() {
            $('#kapal_peminjam_id').val('');
            $('#nama_nahkoda_temp').val('');
            $('#nip_nahkoda_temp').val('');
            $('#nama_kkm_temp').val('');
            $('#nip_kkm_temp').val('');
        }

        function clearKapalData() {
            $('#kapal_id').val('');
            $('#code_kapal').val('');
            $('#alamat_upt').val('');
            $('#lokasi_surat').val('');
            $('#jabatan_staf_pangkalan').val('');
            $('#nama_staf_pagkalan').val('');
            $('#nip_staf').val('');
            $('#nama_nahkoda').val('');
            $('#nip_nahkoda').val('');
            $('#nama_kkm').val('');
            $('#nip_kkm').val('');
            $('#link_ba').val('');
            $('#volume_sebelum').val('');
            calculateVolumeSisa(); // Hitung ulang setelah clear
        }

        function calculateVolumeSisa() {
            const volumeSebelum = parseFloat($('#volume_sebelum').val().replace(/,/, '.')) || 0;
            const volumePemakaian = parseFloat($('#volume_pemakaian').val().replace(/,/, '.')) || 0;

            const volumeSisa = volumeSebelum - volumePemakaian;
            $('#volume_sisa').val(volumeSisa.toFixed(2));
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
                            <div class="font-medium">Nomor : ${ba.nomor_surat}</div>
                            <div class="text-gray-500 dark:text-gray-400">Tanggal : ${formatDate(ba.tanggal_surat)}</div>
                            <div class="text-gray-500 dark:text-gray-400">Jam : ${ba.jam_surat} ${ba.zona_waktu_surat || ''}</div>
                            </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">Kapal : ${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</div>
                            <div class="text-gray-500 dark:text-gray-400">Lokasi : ${ba.lokasi_surat || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${formatNumber(ba.volume_sisa)} Liter</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-${getStatusColor(ba.status_trans)}-100 text-${getStatusColor(ba.status_trans)}-800 dark:bg-${getStatusColor(ba.status_trans)}-900 dark:text-${getStatusColor(ba.status_trans)}-200">
                            ${getStatusText(ba.status_trans)}
                        </span>
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

            // Setup upload modal handlers setelah tabel di-render ulang
            $('.open-upload-modal-btn').off('click').on('click', function() {
                currentBaId = $(this).data('id');
                resetUploadForm(); // Pastikan form upload bersih
                $('#uploadModal').removeClass('hidden');
            });
            $('.view-document-btn').off('click').on('click', handleViewDocument);
        }

        function renderPagination(pagination) {
            const paginationContainer = $('#paginationContainer');
            paginationContainer.empty();

            if (pagination.last_page <= 1) return;
            const paginationHtml = ` <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Menampilkan <span class="font-medium">${pagination.from || 0}</span> sampai <span class="font-medium">${pagination.to || 0}</span> dari <span class="font-medium">${pagination.total}</span> data
                </div>
                <div class="flex items-center space-x-2">
           <button onclick="changePage(${pagination.current_page - 1})" ${pagination.current_page <=1 ? 'disabled' : '' } class="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Halaman ${pagination.current_page} dari ${pagination.last_page}
                    </span>
           <button onclick="changePage(${pagination.current_page + 1})" ${pagination.current_page>= pagination.last_page ? 'disabled' : ''} class="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
            paginationContainer.html(paginationHtml);
        }

        function renderViewModal(data) {
            // ... (fungsi renderViewModal tetap sama) ...
            const content = `
       <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
           <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
               <h6 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Informasi BA</h6>
               <div class="space-y-3">
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor BA:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.nomor_surat}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatDate(data.tanggal_surat)}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Jam:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.jam_surat} ${data.zona_waktu_surat || ''}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.lokasi_surat || '-'}</span>
                   </div>
               </div>
           </div>
           <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
               <h6 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4">Informasi Kapal</h6>
               <div class="space-y-3">
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Kapal:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.kapal ? data.kapal.nama_kapal : '-'}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Kode Kapal:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.kapal_code || '-'}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">UPT:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.kapal && data.kapal.upt ? data.kapal.upt.nama : '-'}</span>
                   </div>
               </div>
           </div>
       </div>
       <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
           <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
               <h6 class="text-lg font-semibold text-orange-900 dark:text-orange-100 mb-4">Detail Peminjaman BBM</h6>
               <div class="space-y-3">
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Jenis BBM:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.keterangan_jenis_bbm || '-'}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">BBM Tersedia:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatNumber(data.volume_sebelum || 0)} Liter</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Jumlah Dipinjam:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatNumber(data.volume_pemakaian || 0)} Liter</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Sisa BBM:</span>
                       <span class="text-sm font-bold text-blue-600 dark:text-blue-400">${formatNumber(data.volume_sisa || 0)} Liter</span>
                   </div>
               </div>
           </div>
           <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
               <h6 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4">Informasi Persetujuan</h6>
               <div class="space-y-3">
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor Persetujuan:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.nomer_persetujuan || '-'}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Persetujuan:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatDate(data.tgl_persetujuan)}</span>
                   </div>
                   <div class="flex justify-between">
                       <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Alasan Peminjaman:</span>
                       <span class="text-sm font-semibold text-gray-900 dark:text-white">${data.sebab_temp || '-'}</span>
                   </div>
               </div>
           </div>
       </div>
       `;
            $('#viewBaContent').html(content);
        }

        function fillEditForm(data) {
            // Set edit mode terlebih dahulu
            currentEditMode = true;
            currentBaId = data.trans_id;

            // Reset form untuk memastikan semua field bersih
            $('#baForm')[0].reset();
            $('#modalTitle').text('Edit BA Peminjaman BBM');
            $('#submitBtn').html('<i class="fas fa-save me-2"></i>Update BA');

            // Set kapal_id dan data kapal dari database (tidak load dari server)
            $('#kapal_id').val(data.kapal ? data.kapal.m_kapal_id : '');
            $('#code_kapal').val(data.kapal_code || (data.kapal ? data.kapal.code_kapal : ''));
            $('#alamat_upt').val(data.alamat_upt || (data.kapal && data.kapal.upt ? data.kapal.upt.alamat1 : ''));
            $('#lokasi_surat').val(data.lokasi_surat || '');
            $('#link_ba').val(data.link_modul_ba || '');
            $('#nomor_surat').val(data.nomor_surat || '');
            // Format tanggal untuk input date (YYYY-MM-DD)
            $('#tanggal_surat').val(formatDateForInput(data.tanggal_surat) || '');
            $('#jam_surat').val(formatTimeForInput(data.jam_surat) || '');
            $('#zona_waktu_surat').val(data.zona_waktu_surat || '');
            $('#kapal_peminjam_id').val(data.kapal_peminjam_id || '');
            $('#volume_sebelum').val(data.volume_sebelum || '');
            $('#volume_pemakaian').val(data.volume_pemakaian || '');
            $('#volume_sisa').val(data.volume_sisa || '');
            $('#keterangan_jenis_bbm').val(data.keterangan_jenis_bbm || '');
            $('#sebab_temp').val(data.sebab_temp || '');
            $('#nomer_persetujuan').val(data.nomer_persetujuan || '');
            $('#tgl_persetujuan').val(formatDateForInput(data.tgl_persetujuan) || '');
            $('#m_persetujuan_id').val(data.m_persetujuan_id || '');

            // Set data kapal peminjam
            $('#nama_nahkoda_temp').val(data.nama_nahkoda_temp || '');
            $('#pangkat_nahkoda_temp').val(data.pangkat_nahkoda_temp || '');
            $('#nip_nahkoda_temp').val(data.nip_nahkoda_temp || '');
            $('#nama_kkm_temp').val(data.nama_kkm_temp || '');
            $('#nip_kkm_temp').val(data.nip_kkm_temp || '');
            $('#an_nakhoda_temp').prop('checked', data.an_nakhoda_temp == 1);
            $('#an_kkm_temp').prop('checked', data.an_kkm_temp == 1);
            $('#pangkat_nahkoda').val(data.pangkat_nahkoda || '');
            $('#jabatan_staf_pangkalan').val(data.jabatan_staf_pangkalan || '');
            $('#nama_staf_pagkalan').val(data.nama_staf_pagkalan || '');
            $('#nip_staf').val(data.nip_staf || '');
            $('#nama_nahkoda').val(data.nama_nahkoda || '');
            $('#nip_nahkoda').val(data.nip_nahkoda || '');
            $('#nama_kkm').val(data.nama_kkm || '');
            $('#nip_kkm').val(data.nip_kkm || '');

            // Set checkboxes dan pemicu perubahan untuk hidden input
            $('#an_staf').prop('checked', data.an_staf == 1).trigger('change');
            $('#an_nakhoda').prop('checked', data.an_nakhoda == 1).trigger('change');
            $('#an_kkm').prop('checked', data.an_kkm == 1).trigger('change');

            // Clear validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        // --- Core Event Handlers ---

        function setupEventHandlers() {
            // Help modal handlers
            $('#helpBtn').on('click', function() {
                $('#helpModal').removeClass('hidden');
            });

            $('#closeHelpModal').on('click', function() {
                $('#helpModal').addClass('hidden');
            });

            // Close help modal when clicking outside
            $('#helpModal').on('click', function(e) {
                if (e.target === this) {
                    $('#helpModal').addClass('hidden');
                }
            });

            // Filter handlers
            $('#search, #kapal, #date_from, #date_to').on('change keyup', debounce(function() {
                currentPage = 1;
                loadData();
            }, 500));

            // Per page handler
            $('#perPage').on('change', function() {
                perPage = parseInt($(this).val());
                currentPage = 1;
                loadData();
            });

            // Kapal selection handler
            $('#kapal_id').on('change', function() {
                const kapalId = $(this).val();
                if (kapalId) {
                    if (!currentEditMode) {
                        // Mode create: auto-fill semua data kapal
                        loadKapalData(kapalId, false);
                        loadBaData(kapalId);
                    } else {
                        // Mode edit: hanya isi field yang kosong, jangan timpa data database
                        loadKapalData(kapalId, true);
                    }
                } else {
                    clearKapalData();
                }
            });

            // Tanggal surat berubah, coba load data BA sebelumnya
            $('#tanggal_surat').on('change', function() {
                const kapalId = $('#kapal_id').val();
                if (kapalId && !currentEditMode) {
                    loadBaData(kapalId);
                }
            });

            // Kapal peminjam selection handler
            $('#kapal_peminjam_id').on('change', function() {
                const kapalPeminjamId = $(this).val();
                if (kapalPeminjamId) {
                    loadKapalPeminjamData(kapalPeminjamId);
                } else {
                    clearKapalPeminjamData();
                }
            });

            // Volume calculation handlers
            // Mengganti `on('input')` dengan `on('change keyup')` dan .replace(/,/, '.')
            $('#volume_sebelum, #volume_pemakaian').on('change keyup', function() {
                // Bersihkan input dari karakter selain angka dan titik/koma
                let val = $(this).val().replace(/[^\d.,]/g, '');
                // Ganti koma dengan titik untuk perhitungan
                $(this).val(val.replace(/,/, '.'));
                calculateVolumeSisa();
            });

            // Form submission
            $('#baForm').on('submit', handleFormSubmit);

            // Checkbox handlers untuk mengisi hidden input
            $('#an_staf, #an_nakhoda, #an_kkm').on('change', function() {
                const hiddenInput = $(this).next('input[type="hidden"]');
                if (hiddenInput.length) {
                    hiddenInput.val($(this).is(':checked') ? '1' : '0');
                }
            });

            // Create BA button
            $('#createBaBtn').on('click', function() {
                resetForm();
                $('#baModal').removeClass('hidden');

                // Set default jam dan zona waktu setelah modal terbuka
                setTimeout(function() {
                    const now = new Date();
                    const currentTime = now.toTimeString().slice(0, 5); // HH:MM format
                    $('#jam_surat').val(currentTime);
                    $('#zona_waktu_surat').val('WIB');
                }, 100);
            });

            // Modal close handlers
            $('#closeModal, #cancelBtn').on('click', function() {
                $('#baModal').addClass('hidden');
            });

            $('#closeUploadModal, #cancelUploadBtn').on('click', function() {
                $('#uploadModal').addClass('hidden');
            });

            $('#closeViewModal, #closeViewModalBtn').on('click', function() {
                $('#viewBaModal').addClass('hidden');
            });

            $('#closeViewDocumentModal, #closeViewDocumentModalBtn, #deleteDocumentBtn').on('click', function(e) {
                // Jika tombol hapus, jalankan fungsi hapus, jika tidak hanya sembunyikan modal
                if (e.currentTarget.id === 'deleteDocumentBtn') {
                    const baId = $(this).data('ba-id');
                    if (baId) {
                        deleteDocument(baId);
                    }
                } else {
                    $('#viewDocumentModal').addClass('hidden');
                    $('#documentViewer').attr('src', ''); // Bersihkan iframe
                }
            });

            // ESC key to close modals
            $(document).keydown(function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    $('.modal-container:not(.hidden)').addClass('hidden'); // Tutup semua modal terbuka
                }
            });

            // Click outside to close modals
            $(document).on('click', function(e) {
                if ($(e.target).hasClass('modal-container')) {
                    $(e.target).addClass('hidden');
                }
            });

            // Upload form
            $('#uploadForm').on('submit', handleUploadSubmit);

            // Clear filter button
            $('#clearFilterBtn').on('click', function() {
                window.clearFilter(); // Panggil fungsi global
            });

            // Create first BA button
            $(document).on('click', '#createFirstBaBtn', function() {
                resetForm();
                $('#baModal').removeClass('hidden');
            });

            // Document modal handlers
            $('#closeUploadModal').on('click', function() {
                $('#uploadModal').addClass('hidden');
            });

            $('#cancelUploadBtn').on('click', function() {
                $('#uploadModal').addClass('hidden');
            });

            $('#closeViewDocumentModal').on('click', function() {
                $('#viewDocumentModal').addClass('hidden');
            });

            $('#deleteDocumentBtn').on('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                    $.ajax({
                        url: `/ba-peminjaman-bbm/${currentBaId}/delete-document`
                        , type: 'DELETE'
                        , data: {
                            _token: '{{ csrf_token() }}'
                        }
                        , success: function(response) {
                            if (response.success) {
                                showSuccess('Dokumen berhasil dihapus');
                                $('#viewDocumentModal').addClass('hidden');
                                loadData(); // Reload data to update table
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
            });
        }

        // --- AJAX Submission Handlers ---

        function handleFormSubmit(e) {
            e.preventDefault();

            const formData = new FormData(this);

            let url = '{{ route("ba-peminjaman-bbm.store") }}';
            let method = 'POST';

            if (currentEditMode) {
                url = `{{ url('ba-peminjaman-bbm') }}/${currentBaId}`;
                // Karena menggunakan FormData, kita harus menambahkan _method: PUT secara manual
                // Laravel akan membaca ini jika kita mengirimkannya sebagai POST dengan field _method.
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url
                , type: 'POST', // Selalu POST untuk FormData (termasuk PUT/DELETE via _method)
                data: formData
                , processData: false
                , contentType: false
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , beforeSend: function() {
                    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');
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
                        showError('Terjadi kesalahan saat menyimpan data');
                        console.error('AJAX Error handleFormSubmit:', xhr);
                    }
                }
                , complete: function() {
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan BA');
                }
            });
        }

        function handleUploadSubmit(e) {
            e.preventDefault();
            e.stopPropagation();

            if (!currentBaId) {
                showError('ID BA tidak ditemukan');
                return;
            }

            const formData = new FormData(this);

            $.ajax({
                url: `{{ url('ba-peminjaman-bbm') }}/${currentBaId}/upload`
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = (evt.loaded / evt.total) * 100;
                            $('#uploadProgress').removeClass('hidden').show();
                            $('#progressBar').css('width', percentComplete + '%');
                            $('#progressText').text(`Uploading... ${Math.round(percentComplete)}%`);
                        }
                    }, false);
                    return xhr;
                }
                , beforeSend: function() {
                    $('#uploadProgress').removeClass('hidden').show();
                    $('#uploadBtn').prop('disabled', true);
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
                    console.error('AJAX Error handleUploadSubmit:', xhr);
                }
                , complete: function() {
                    $('#uploadProgress').addClass('hidden');
                    $('#uploadBtn').prop('disabled', false);
                    $('#uploadProgress .progress-bar').css('width', '0%');
                }
            });
        }

        function handleViewDocument() {
            const baId = $(this).data('id');

            $.ajax({
                url: `{{ url('ba-peminjaman-bbm') }}/${baId}/view-document`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        $('#documentViewer').attr('src', response.file_url);
                        $('#viewDocumentModalLabel').text(`Dokumen: ${response.filename}`);
                        $('#viewDocumentModal').removeClass('hidden');

                        // Update delete button data-id
                        $('#deleteDocumentBtn').data('ba-id', baId);
                        $('#deleteDocumentBtn').show(); // Tampilkan tombol hapus
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal membuka dokumen');
                    console.error('AJAX Error handleViewDocument:', xhr);
                }
            });
        }

        function deleteDocument(baId) {
            if (!confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                return;
            }

            $.ajax({
                url: `{{ url('ba-peminjaman-bbm') }}/${baId}/delete-document`
                , type: 'POST', // Menggunakan POST dengan method override
                data: {
                    _method: 'DELETE'
                }
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

        // --- Global Functions (exposed to window for `onclick` attributes) ---

        window.openModal = function() {
            resetForm();
            $('#modalTitle').text('Form Tambah BA Peminjaman BBM');
            $('#baModal').removeClass('hidden');
        };

        window.clearFilter = function() {
            $('#filterForm')[0].reset();
            $('#perPage').val(10);
            perPage = 10;
            currentPage = 1;
            loadData();
        };

        window.refreshData = function() {
            loadData();
        };

        window.exportData = function() {
            showSuccess('Fitur export akan segera tersedia');
        };

        window.changePage = function(page) {
            currentPage = page;
            loadData();
        };

        window.viewBa = function(id) {
            $.ajax({
                url: `{{ url('ba-peminjaman-bbm') }}/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        renderViewModal(response.data);
                        $('#viewBaModal').removeClass('hidden');
                    } else {
                        showError('Gagal memuat detail BA');
                    }
                }
                , error: function(xhr) {
                    showError('Terjadi kesalahan saat memuat detail BA');
                    console.error('AJAX Error viewBa:', xhr);
                }
            });
        };

        window.editBa = function(id) {
            $.ajax({
                url: `{{ url('ba-peminjaman-bbm') }}/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        fillEditForm(response.data);
                        currentBaId = id;
                        currentEditMode = true;
                        $('#modalTitle').text('Edit BA Peminjaman BBM');
                        $('#submitBtn').html('<i class="fas fa-save me-2"></i>Update BA');
                        $('#baModal').removeClass('hidden');
                    } else {
                        showError('Gagal memuat data BA untuk diedit');
                    }
                }
                , error: function(xhr) {
                    showError('Terjadi kesalahan saat memuat data BA untuk diedit');
                    console.error('AJAX Error editBa:', xhr);
                }
            });
        };

        window.deleteBa = function(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus BA ini?')) {
                return;
            }

            $.ajax({
                url: `{{ url('ba-peminjaman-bbm') }}/${id}`
                , type: 'POST', // Menggunakan POST dengan method override
                data: {
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

        // Notification function
        function showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-[100000] p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;

            // Set notification styles based on type
            switch (type) {
                case 'success':
                    notification.className += ' bg-green-500 text-white';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>${message}</span>
                        </div>
                    `;
                    break;
                case 'error':
                    notification.className += ' bg-red-500 text-white';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>${message}</span>
                        </div>
                    `;
                    break;
                case 'info':
                    notification.className += ' bg-blue-500 text-white';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>${message}</span>
                        </div>
                    `;
                    break;
                default:
                    notification.className += ' bg-gray-500 text-white';
                    notification.innerHTML = `<span>${message}</span>`;
            }

            // Add close button
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `;
            closeBtn.className = 'ml-2 text-white hover:text-gray-200 transition-colors';
            closeBtn.onclick = () => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            };

            notification.querySelector('div').appendChild(closeBtn);

            // Add to DOM
            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        window.generatePdf = function(baId) {
            // Show loading
            showNotification('info', 'Sedang membuat PDF...');

            $.ajax({
                url: `/ba-peminjaman-bbm/${baId}/pdf`
                , type: 'GET'
                , timeout: 30000, // 30 seconds timeout
                success: function(response) {
                    if (response.success) {
                        // Open PDF in new tab
                        window.open(response.download_url, '_blank');
                        showNotification('success', 'PDF berhasil dibuat dan dibuka di tab baru');
                    } else {
                        showNotification('error', response.message || 'Gagal membuat PDF');
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else if (xhr.status === 404) {
                        showNotification('error', 'Data BA tidak ditemukan');
                    } else if (xhr.status === 500) {
                        showNotification('error', 'Terjadi kesalahan server saat membuat PDF');
                    } else if (xhr.statusText === 'timeout') {
                        showNotification('error', 'Timeout: Proses pembuatan PDF terlalu lama');
                    } else {
                        const error = xhr.responseJSON;
                        showNotification('error', error ? error.message : 'Gagal membuat PDF');
                    }
                    console.error('AJAX Error generatePdf:', xhr);
                }
            });
        };

        // Document modal functions
        window.openUploadModal = function(baId) {
            currentBaId = baId;

            // Check if BA already has a file to determine modal title
            const baRow = $(`button[onclick="viewDocument(${baId})"]`).closest('tr');
            const hasFile = baRow.length > 0 && baRow.find('button[onclick="viewDocument(' + baId + ')"]').length > 0;

            if (hasFile) {
                $('#uploadModalTitle').text('Ganti Dokumen Pendukung');
                $('#uploadBtnText').text('Ganti Dokumen');
                $('#uploadBtn').removeClass('bg-green-600 hover:bg-green-700').addClass('bg-orange-600 hover:bg-orange-700');
            } else {
                $('#uploadModalTitle').text('Upload Dokumen Pendukung');
                $('#uploadBtnText').text('Upload');
                $('#uploadBtn').removeClass('bg-orange-600 hover:bg-orange-700').addClass('bg-green-600 hover:bg-green-700');
            }

            $('#uploadModal').removeClass('hidden');
            $('#uploadForm')[0].reset();
            $('#uploadProgress').addClass('hidden');
            $('#documentFile').focus();
        };

        window.viewDocument = function(baId) {
            currentBaId = baId;
            showError('Memuat dokumen...');

            $.ajax({
                url: `/ba-peminjaman-bbm/${baId}/view-document`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        $('#documentViewer').html(`
                            <div class="text-center">
                                <div class="mb-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">${response.filename}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Dokumen pendukung BA Peminjaman BBM</p>
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
                    if (xhr.status === 404) {
                        showError('Dokumen tidak ditemukan');
                    } else {
                        showError('Gagal memuat dokumen');
                    }
                    console.error('AJAX Error viewDocument:', xhr);
                }
            });
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
                        <h3 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-white">Form Tambah BA Peminjaman BBM</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data berikut untuk membuat Berita Acara Peminjaman BBM</p>
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
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Informasi Kapal
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="kapal_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Pilih Kapal <span class="text-red-500">*</span>
                                </label>
                                <select id="kapal_id" name="kapal_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">-- Pilih Kapal --</option>
                                    @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->m_kapal_id }}">{{ $kapal->nama_kapal }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pilih kapal yang akan dibuatkan BA-nya</p>
                            </div>
                            <div>
                                <label for="code_kapal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kode Kapal</label>
                                <input type="text" id="code_kapal" name="code_kapal" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kode kapal akan terisi otomatis</p>
                            </div>
                        </div>
                    </div>

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
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Alamat UPT akan terisi otomatis</p>
                            </div>
                            <div>
                                <label for="lokasi_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Lokasi Pembuatan BA <span class="text-red-500">*</span>
                                </label>
                                <textarea id="lokasi_surat" name="lokasi_surat" rows="3" required readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed resize-none"></textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lokasi akan terisi otomatis dari data kapal</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div class="md:col-span-1 lg:col-span-2">
                                <label for="nomor_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nomor BA <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nomor_surat" name="nomor_surat" required placeholder="BA/001/KKP/2024" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="tanggal_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" id="tanggal_surat" name="tanggal_surat" required class="w-full pl-4 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors" placeholder="Pilih tanggal">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Klik untuk memilih tanggal</p>
                            </div>
                            <div>
                                <label for="jam_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jam <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="jam_surat" name="jam_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
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
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Informasi Kapal Peminjam
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="kapal_peminjam_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Kapal Peminjam <span class="text-red-500">*</span>
                                </label>
                                <select id="kapal_peminjam_id" name="kapal_peminjam_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">-- Pilih Kapal Peminjam --</option>
                                    @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->m_kapal_id }}">{{ $kapal->nama_kapal }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pilih kapal yang akan meminjam BBM</p>
                            </div>
                            <div>
                                <label for="link_ba" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Link BA</label>
                                <input type="text" id="link_ba" name="link_ba" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">BA sebelumnya yang akan di-link</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-yellow-900 dark:text-yellow-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Informasi Persetujuan
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="m_persetujuan_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jenis Persetujuan <span class="text-red-500">*</span>
                                </label>
                                <select id="m_persetujuan_id" name="m_persetujuan_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">-- Pilih Jenis Persetujuan --</option>
                                    @foreach($persetujuans as $persetujuan)
                                    <option value="{{ $persetujuan->id }}">{{ $persetujuan->deskripsi_persetujuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nomer_persetujuan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nomor Persetujuan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nomer_persetujuan" name="nomer_persetujuan" required placeholder="Nomor surat persetujuan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="tgl_persetujuan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Tanggal Persetujuan <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tgl_persetujuan" name="tgl_persetujuan" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-orange-900 dark:text-orange-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                            </svg>
                            Detail Peminjaman BBM
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="keterangan_jenis_bbm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jenis BBM <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" required placeholder="BIO SOLAR" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="volume_sebelum" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    BBM Sebelum Pengisian (Liter) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="volume_sebelum" name="volume_sebelum" step="0.01" min="0" required readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Volume BBM tersedia untuk dipinjamkan</p>
                            </div>
                            <div>
                                <label for="volume_pemakaian" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jumlah BBM Di Pinjamkan (Liter) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="volume_pemakaian" name="volume_pemakaian" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-colors">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Volume BBM yang akan dipinjamkan</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="volume_sisa" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Sisa BBM (Liter) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="volume_sisa" name="volume_sisa" step="0.01" min="0" required readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Volume BBM tersisa setelah peminjaman</p>
                            </div>
                            <div>
                                <label for="sebab_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Alasan Peminjaman BBM <span class="text-red-500">*</span>
                                </label>
                                <textarea id="sebab_temp" name="sebab_temp" rows="3" required placeholder="Masukkan alasan peminjaman BBM..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-colors resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-yellow-900 dark:text-yellow-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Data Kapal Peminjam
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="nama_nahkoda_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Nakhoda Kapal Peminjam</label>
                                <input type="text" id="nama_nahkoda_temp" name="nama_nahkoda_temp" placeholder="Nama nakhoda kapal peminjam" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="pangkat_nahkoda_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pangkat Nakhoda</label>
                                <input type="text" id="pangkat_nahkoda_temp" name="pangkat_nahkoda_temp" placeholder="Pangkat nakhoda kapal peminjam" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_nahkoda_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP Nakhoda</label>
                                <input type="text" id="nip_nahkoda_temp" name="nip_nahkoda_temp" placeholder="NIP nakhoda kapal peminjam" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kkm_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama KKM Kapal Peminjam</label>
                                <input type="text" id="nama_kkm_temp" name="nama_kkm_temp" placeholder="Nama KKM kapal peminjam" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_kkm_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP KKM</label>
                                <input type="text" id="nip_kkm_temp" name="nip_kkm_temp" placeholder="NIP KKM kapal peminjam" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_nakhoda_temp" name="an_nakhoda_temp" value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                <label for="an_nakhoda_temp" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    An. Nakhoda Kapal Peminjam
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="an_kkm_temp" name="an_kkm_temp" value="1" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                <label for="an_kkm_temp" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    An. KKM Kapal Peminjam
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Pejabat/Staf UPT (Menyaksikan)
                        </h4>
                        <div class="mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_staf" name="an_staf" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="an_staf" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tandai "An." di depan nama
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="jabatan_staf_pangkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jabatan</label>
                                <input type="text" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" placeholder="Jabatan pejabat UPT" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nama_staf_pagkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama</label>
                                <input type="text" id="nama_staf_pagkalan" name="nama_staf_pagkalan" placeholder="Nama lengkap" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_staf" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_staf" name="nip_staf" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-cyan-900 dark:text-cyan-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Nakhoda Kapal
                        </h4>
                        <div class="mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_nakhoda" name="an_nakhoda" value="1" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                                <label for="an_nakhoda" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tandai "An." di depan nama
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="nama_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Nakhoda</label>
                                <input type="text" id="nama_nahkoda" name="nama_nahkoda" placeholder="Nama lengkap nakhoda" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="pangkat_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pangkat Nakhoda</label>
                                <input type="text" id="pangkat_nahkoda" name="pangkat_nahkoda" placeholder="Pangkat nakhoda" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_nahkoda" name="nip_nahkoda" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-red-900 dark:text-red-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            KKM (Kepala Kamar Mesin)
                        </h4>
                        <div class="mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_kkm" name="an_kkm" value="1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="an_kkm" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tandai "An." di depan nama
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama KKM</label>
                                <input type="text" id="nama_kkm" name="nama_kkm" placeholder="Nama lengkap KKM" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label for="nip_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_kkm" name="nip_kkm" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
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
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail BA Peminjaman BBM</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap Berita Acara Peminjaman BBM</p>
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

                <div id="uploadProgress" class="hidden mb-4">
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div id="progressBar" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="progressText" class="text-sm text-gray-600 dark:text-gray-400 mt-1">Uploading...</p>
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

<!-- Help Modal -->
<div id="helpModal" class="fixed inset-0 bg-black bg-opacity-50 z-[99999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Bantuan - BA Peminjaman BBM</h3>
                <button id="closeHelpModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="space-y-6">
                    <!-- Overview -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Apa itu BA Peminjaman BBM?</h4>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            BA (Berita Acara) Peminjaman BBM adalah dokumen resmi yang mencatat proses peminjaman BBM dari kapal lain.
                            Dokumen ini digunakan untuk melacak dan mengatur distribusi BBM antar kapal.
                        </p>
                    </div>

                    <!-- Form Sections -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Bagian-bagian Form</h4>
                        <div class="space-y-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">📋 Informasi BA</h5>
                                <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                    <li>• <strong>Nomor Surat:</strong> Nomor unik untuk identifikasi BA</li>
                                    <li>• <strong>Tanggal & Jam:</strong> Waktu pembuatan dokumen</li>
                                    <li>• <strong>Lokasi:</strong> Tempat pembuatan BA</li>
                                </ul>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <h5 class="font-semibold text-green-900 dark:text-green-100 mb-2">🚢 Informasi Kapal</h5>
                                <ul class="text-sm text-green-800 dark:text-green-200 space-y-1">
                                    <li>• <strong>Pilih Kapal:</strong> Kapal yang akan meminjam BBM</li>
                                    <li>• <strong>Data Otomatis:</strong> Nama nahkoda, KKM, dan info kapal terisi otomatis</li>
                                </ul>
                            </div>

                            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                                <h5 class="font-semibold text-orange-900 dark:text-orange-100 mb-2">⛽ Informasi BBM</h5>
                                <ul class="text-sm text-orange-800 dark:text-orange-200 space-y-1">
                                    <li>• <strong>Jenis BBM:</strong> Tipe bahan bakar yang dipinjam</li>
                                    <li>• <strong>Volume:</strong> Jumlah BBM yang dipinjam</li>
                                    <li>• <strong>Alasan:</strong> Tujuan peminjaman BBM</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Steps -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Langkah-langkah Penggunaan</h4>
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-semibold">1</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Klik "Tambah BA"</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Buka form untuk membuat BA baru</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-semibold">2</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Isi Informasi BA</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Masukkan nomor surat, tanggal, dan lokasi</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-semibold">3</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Pilih Kapal</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Pilih kapal yang akan meminjam BBM</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-semibold">4</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Isi Detail BBM</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Masukkan jenis, volume, dan alasan peminjaman</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-semibold">5</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Simpan BA</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Klik "Simpan BA" untuk menyimpan dokumen</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">💡 Tips & Catatan</h4>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                            <ul class="text-sm text-yellow-800 dark:text-yellow-200 space-y-2">
                                <li>• Pastikan nomor surat unik dan tidak duplikat</li>
                                <li>• Periksa data kapal sebelum menyimpan</li>
                                <li>• Volume BBM harus sesuai dengan kebutuhan</li>
                                <li>• Alasan peminjaman harus jelas dan logis</li>
                                <li>• BA yang sudah disimpan dapat diedit atau dihapus</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
