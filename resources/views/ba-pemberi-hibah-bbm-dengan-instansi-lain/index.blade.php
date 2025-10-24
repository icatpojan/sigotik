@extends('layouts.dashboard')

@section('title', 'BA Pemberi Hibah BBM Dengan Instansi Lain')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">BA Pemberi Hibah BBM Dengan Instansi Lain</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola Berita Acara Pemberi Hibah BBM Dengan Instansi Lain</p>
        </div>
        <div class="flex gap-2">
            <button id="helpBtn" class="inline-flex items-center px-4 py-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </button>
            <button id="createBaBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah BA
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cari BA/Kapal</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari nomor surat..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full sm:w-40">
                    <label for="kapal" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Filter Kapal</label>
                    <select id="kapal" name="kapal" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Kapal</option>
                        @foreach($kapals as $kapal)
                        <option value="{{ $kapal->code_kapal }}">{{ $kapal->nama_kapal }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label for="date_from" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Dari</label>
                    <input type="date" id="date_from" name="date_from" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="w-full sm:w-40">
                    <label for="date_to" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Sampai</label>
                    <input type="date" id="date_to" name="date_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="w-full sm:w-32">
                    <label for="perPage" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Per Halaman</label>
                    <select id="perPage" name="per_page" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto">
                    <button type="button" id="clearFilterBtn" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nomor Surat & Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kapal Pemberi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Instansi Penerima</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Volume Hibah</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="baTableBody" class="bg-white dark:bg-gray-800">
                </tbody>
            </table>
        </div>

        <div id="loadingIndicator" class="hidden items-center justify-center py-8">
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span>
            </div>
        </div>

        <div id="paginationContainer" class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
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
        let currentPage = 1;
        let perPage = 10;
        let currentBaId = null;
        let currentEditMode = false;

        loadData();
        setupEventHandlers();
        setDefaultDates();
        setupDatePickers();

        function formatDateForInput(dateString) {
            if (!dateString) return '';
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) return dateString;
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            } catch (e) {
                return '';
            }
        }

        function formatTimeForInput(timeString) {
            if (!timeString) return '';
            if (/^\d{2}:\d{2}$/.test(timeString)) return timeString;
            if (/^\d{2}:\d{2}:\d{2}$/.test(timeString)) return timeString.substring(0, 5);
            return '';
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(...args), wait);
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

        function showLoading() {
            $('#baTableBody').html('<tr><td colspan="5" class="px-6 py-4 text-center"><div class="flex items-center justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div></td></tr>');
        }

        function hideLoading() {}

        function showSuccess(message) {
            if (typeof toastr !== 'undefined') toastr.success(message);
        }

        function showError(message) {
            if (typeof toastr !== 'undefined') toastr.error(message);
        }

        function setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            const currentTime = new Date().toTimeString().slice(0, 5); // Format HH:MM

            if ($('#tanggal_surat').length) $('#tanggal_surat').val(today);
            if ($('#jam_surat').length) $('#jam_surat').val(currentTime);
        }

        function setupDatePickers() {
            $('input[type="date"], input[type="time"]').each(function() {
                $(this).on('click focus', function(e) {
                    e.preventDefault();
                    this.showPicker && this.showPicker();
                }).css({
                    'cursor': 'pointer'
                    , 'background-color': 'transparent'
                });
            });
        }

        function resetForm() {
            $('#baForm')[0].reset();
            $('#modalTitle').text('Form Tambah BA Pemberi Hibah BBM Dengan Instansi Lain');
            $('#submitBtn').html('Simpan BA');
            currentBaId = null;
            currentEditMode = false;
            clearKapalData();
            setDefaultDates();
            $('#an_staf, #an_nakhoda, #an_kkm').prop('checked', false);
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
                input.after(`<div class="text-red-500 text-sm mt-1">${errorMessage}</div>`);
            });
        }

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
                url: '{{ route("ba-pemberi-hibah-bbm-dengan-instansi-lain.data") }}'
                , type: 'GET'
                , data: params
                , beforeSend: showLoading
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
                }
                , complete: hideLoading
            });
        }

        function loadKapalData(kapalId) {
            return new Promise((resolve) => {
                $.ajax({
                    url: '{{ route("ba-pemberi-hibah-bbm-dengan-instansi-lain.kapal-data") }}'
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
                            $('#lokasi_surat').val(data.kota);

                            // PEJABAT/STAF UPT
                            $('#nama_petugas').val(data.nama_petugas);
                            $('#nip_petugas').val(data.nip_petugas);
                            $('#jabatan_staf_pangkalan').val(data.jabatan_petugas);

                            // NAKHODA
                            $('#nama_nahkoda').val(data.nama_nakoda);
                            $('#nip_nahkoda').val(data.nip_nakoda);
                            $('#pangkat_nahkoda').val(data.pangkat_nahkoda);

                            // KKM
                            $('#nama_kkm').val(data.nama_kkm);
                            $('#nip_kkm').val(data.nip_kkm);

                            // Load LINK BA dan volume dari BA Penerimaan BBM
                            getVolumeSounding();
                        } else {
                            clearKapalData();
                        }
                        resolve();
                    }
                    , error: function(xhr) {
                        console.error('AJAX Error loadKapalData:', xhr);
                        resolve();
                    }
                });
            });
        }

        function loadPersetujuanData(persetujuanId) {
            $.ajax({
                url: '{{ route("ba-pemberi-hibah-bbm-dengan-instansi-lain.persetujuan-data") }}'
                , type: 'GET'
                , data: {
                    persetujuan_id: persetujuanId
                }
                , success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        // Hanya auto-fill tanggal persetujuan, nomor persetujuan bisa diinput manual
                        $('#tanggal_persetujuan').val(formatDateForInput(data.tanggal_persetujuan));
                    }
                }
            });
        }

        function getVolumeSounding() {
            var tanggal_surat = $("#tanggal_surat").val();
            var kapal_id = $("#kapal_id").val();

            if (tanggal_surat && kapal_id) {
                $.ajax({
                    url: '{{ route("ba-pemberi-hibah-bbm-dengan-instansi-lain.ba-data") }}'
                    , type: 'GET'
                    , data: {
                        tanggal_surat: tanggal_surat
                        , kapal_id: kapal_id
                    }
                    , success: function(response) {
                        if (response.success && response.data) {
                            $('#link_ba').val(response.data.nomor_surat);
                            $('#link_ba_hidden').val(response.data.nomor_surat);
                            $('#volume_sebelum').val(response.data.volume_sisa);
                        } else {
                            $('#link_ba').val('');
                            $('#link_ba_hidden').val('');
                            $('#volume_sebelum').val('');
                        }
                    }
                    , error: function(xhr) {
                        console.error('AJAX Error getVolumeSounding:', xhr);
                    }
                });
            }
        }

        function validateVolume() {
            const volumeSebelum = parseFloat($('#volume_sebelum').val()) || 0;
            const volumePemakaian = parseFloat($('#volume_pemakaian').val()) || 0;

            if (volumePemakaian > volumeSebelum) {
                alert('Volume Hibah Tidak Boleh Melebihi Volume Sounding / Lakukan Sounding Ulang');
                $('#volume_pemakaian').val('');
                $('#volume_sisa').val('');
                $('#submitBtn').prop('disabled', true);
            } else {
                const volumeSisa = volumeSebelum - volumePemakaian;
                $('#volume_sisa').val(volumeSisa.toFixed(2));
                $('#submitBtn').prop('disabled', false);
            }
        }

        function clearKapalData() {
            $('#code_kapal, #alamat_upt, #jabatan_staf_pangkalan, #nama_petugas, #nip_petugas, #nama_nahkoda, #pangkat_nahkoda, #nip_nahkoda, #nama_kkm, #nip_kkm, #link_ba, #link_ba_hidden, #volume_sebelum').val('');
            $('#an_staf, #an_nakhoda, #an_kkm').prop('checked', false);
            // Tidak clear nomor_persetujuan karena bisa diinput manual
        }

        function renderTable(data) {
            const tbody = $('#baTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500"><div class="flex flex-col items-center py-8"><svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><h3 class="text-lg font-medium mb-2">Tidak ada data BA</h3><button id="createFirstBaBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">Tambah BA Pertama</button></div></td></tr>');
                return;
            }

            data.forEach(ba => {
                tbody.append(`
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="font-medium">${ba.nomor_surat}</div>
                            <div class="text-gray-500 text-sm">${formatDate(ba.tanggal_surat)}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="font-medium">${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</div>
                            <div class="text-gray-500 text-sm">${ba.lokasi_surat || '-'}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="font-medium">${ba.kapal_code_temp || '-'}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="font-medium">${formatNumber(ba.volume_pemakaian)} Liter</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
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
                `);
            });
        }

        function renderPagination(pagination) {
            const container = $('#paginationContainer');
            container.empty();
            if (pagination.last_page <= 1) return;

            let html = '<div class="flex items-center justify-between mt-4"><div class="text-sm text-gray-700">';
            html += `Menampilkan ${pagination.from || 0} sampai ${pagination.to || 0} dari ${pagination.total} data</div><div class="flex space-x-1">`;

            if (pagination.current_page > 1) {
                html += `<button onclick="changePage(${pagination.current_page - 1})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</button>`;
            }

            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === pagination.current_page) {
                    html += `<button class="px-3 py-1 text-sm bg-blue-600 text-white border border-blue-600 rounded">${i}</button>`;
                } else if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    html += `<button onclick="changePage(${i})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">${i}</button>`;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    html += '<span class="px-2">...</span>';
                }
            }

            if (pagination.current_page < pagination.last_page) {
                html += `<button onclick="changePage(${pagination.current_page + 1})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>`;
            }

            html += '</div></div>';
            container.html(html);
        }

        function setupEventHandlers() {
            // Help button
            $('#helpBtn').click(function() {
                $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
            });

            $(document).on('click', '#createBaBtn, #createFirstBaBtn', function() {
                resetForm();
                $('#baModal').removeClass('hidden');
            });

            $('#closeModal, #cancelBtn').on('click', () => $('#baModal').addClass('hidden'));
            $('#closeViewModal, #closeViewModalBtn').on('click', () => $('#viewBaModal').addClass('hidden'));
            $('#closeUploadModal, #cancelUploadBtn').on('click', () => $('#uploadModal').addClass('hidden'));
            $('#closeViewDocumentModal').on('click', () => $('#viewDocumentModal').addClass('hidden'));

            // Close help modal
            $('#closeHelpModal').on('click', function() {
                $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
            });

            $('#deleteDocumentBtn').on('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                    deleteDocument();
                }
            });

            $('#kapal_id').on('change', function() {
                const kapalId = $(this).val();
                kapalId ? loadKapalData(kapalId) : clearKapalData();
            });

            $('#tanggal_surat').on('change', function() {
                getVolumeSounding();
            });

            $('#persetujuan_id').on('change', function() {
                const persetujuanId = $(this).val();
                if (persetujuanId) loadPersetujuanData(persetujuanId);
            });

            $('#volume_pemakaian').on('input', function() {
                validateVolume();
            });

            $('#baForm').on('submit', function(e) {
                e.preventDefault();
                saveBa();
            });

            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                uploadDocument();
            });

            $('#search').on('input', debounce(() => {
                currentPage = 1;
                loadData();
            }, 500));
            $('#kapal, #date_from, #date_to, #perPage').on('change', function() {
                currentPage = 1;
                perPage = $('#perPage').val();
                loadData();
            });

            $('#clearFilterBtn').on('click', function() {
                $('#filterForm')[0].reset();
                currentPage = 1;
                perPage = 10;
                loadData();
            });
        }

        function saveBa() {
            const formData = new FormData($('#baForm')[0]);
            const url = currentEditMode ? `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${currentBaId}` : '{{ route("ba-pemberi-hibah-bbm-dengan-instansi-lain.store") }}';

            if (currentEditMode) formData.append('_method', 'PUT');

            $.ajax({
                url: url
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
                        $('#baModal').addClass('hidden');
                        loadData();
                        resetForm();
                    } else {
                        showError(response.message);
                        if (response.errors) displayValidationErrors(response.errors);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        displayValidationErrors(xhr.responseJSON.errors);
                        showError('Validasi gagal. Periksa input Anda.');
                    } else {
                        showError('Gagal menyimpan BA');
                    }
                }
            });
        }

        window.viewBa = function(id) {
            $.ajax({
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        const ba = response.data;
                        let html = `
                            <div class="space-y-6">
                                <!-- Informasi Umum -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Informasi Umum
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor Surat</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nomor_surat || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Surat</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${formatDate(ba.tanggal_surat)}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Jam Surat</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.jam_surat || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Zona Waktu</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.zona_waktu_surat || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi Surat</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.lokasi_surat || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">LINK BA</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.link_modul_ba || '-'}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Kapal -->
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Informasi Kapal
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Kapal Pemberi Hibah</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Kapal Penerima Hibah</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.kapal_code_temp || '-'}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Persetujuan -->
                                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Berdasarkan Persetujuan
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nomor Persetujuan</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nomer_persetujuan || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Persetujuan</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${formatDate(ba.tgl_persetujuan)}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi BBM -->
                                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-orange-900 dark:text-orange-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        Informasi BBM
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Jenis BBM</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.keterangan_jenis_bbm || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">BBM Sebelum Pengisian</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${formatNumber(ba.volume_sebelum)} Liter</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Volume Hibah</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${formatNumber(ba.volume_pemakaian)} Liter</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Sisa BBM</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${formatNumber(ba.volume_sisa)} Liter</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Alasan Hibah BBM</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.sebab_temp || '-'}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pejabat/Staf UPT Kapal Pemberi Hibah -->
                                <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-teal-900 dark:text-teal-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        PEJABAT/STAF UPT KAPAL PEMBERI HIBAH
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.jabatan_staf_pangkalan || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nama_staf_pagkalan || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIP</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nip_staf || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">An.</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.an_staf ? 'Ya' : 'Tidak'}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nakhoda Kapal Pemberi Hibah -->
                                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        NAKHODA KAPAL PEMBERI HIBAH
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nama_nahkoda || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Pangkat/Gol</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.pangkat_nahkoda || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIP</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nip_nahkoda || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">An.</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.an_nakhoda ? 'Ya' : 'Tidak'}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- KKM Kapal Pemberi Hibah -->
                                <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-cyan-900 dark:text-cyan-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        KKM KAPAL PEMBERI HIBAH
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nama_kkm || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIP</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nip_kkm || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">An.</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.an_kkm ? 'Ya' : 'Tidak'}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nakhoda Penerima Hibah -->
                                <div class="bg-pink-50 dark:bg-pink-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-pink-900 dark:text-pink-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        NAKHODA PENERIMA HIBAH
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nama_nahkoda_temp || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Pangkat/Gol</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.pangkat_nahkoda_temp || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIP</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nip_nahkoda_temp || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">An.</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.an_nakhoda_temp ? 'Ya' : 'Tidak'}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- KKM Penerima Hibah -->
                                <div class="bg-rose-50 dark:bg-rose-900/20 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-rose-900 dark:text-rose-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        KKM PENERIMA HIBAH
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nama_kkm_temp || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIP</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.nip_kkm_temp || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">An.</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">${ba.an_kkm_temp ? 'Ya' : 'Tidak'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#viewBaContent').html(html);
                        $('#viewBaModal').removeClass('hidden');
                    } else {
                        showError(response.message);
                    }
                }
            });
        };

        window.editBa = function(id) {
            currentBaId = id;
            currentEditMode = true;

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${id}`
                , type: 'GET'
                , success: function(response) {
                    console.log('Edit response:', response);
                    if (response.success) {
                        console.log('Edit data:', response.data);
                        $('#modalTitle').text('Form Edit BA Pemberi Hibah BBM Dengan Instansi Lain');
                        $('#submitBtn').html('Update BA');
                        $('#baModal').removeClass('hidden');

                        // Wait for modal to be fully rendered before filling form
                        setTimeout(() => {
                            fillEditForm(response.data);
                        }, 100);
                    } else {
                        console.error('Edit failed:', response.message);
                        showError(response.message);
                    }
                }
                , error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr, status, error);
                    showError('Gagal memuat data: ' + error);
                }
            });
        };

        function fillEditForm(ba) {
            console.log('fillEditForm called with data:', ba);
            console.log('Modal visible:', $('#baModal').is(':visible'));
            console.log('Form exists:', $('#baForm').length > 0);
            console.log('nama_staf_pagkalan:', ba.nama_staf_pagkalan);
            console.log('jabatan_staf_pangkalan:', ba.jabatan_staf_pangkalan);
            console.log('nip_staf:', ba.nip_staf);
            $('#baId').val(ba.trans_id);
            // Set kapal_id tanpa trigger change untuk menghindari konflik saat edit
            $('#kapal_id').val(ba.kapal ? ba.kapal.m_kapal_id : '');
            // Load kapal data secara manual untuk edit
            if (ba.kapal) {
                $('#code_kapal').val(ba.kapal.code_kapal);
                $('#alamat_upt').val(ba.kapal.upt ? ba.kapal.upt.alamat1 : '');
            }
            $('#nomor_surat').val(ba.nomor_surat);
            $('#tanggal_surat').val(formatDateForInput(ba.tanggal_surat));
            $('#jam_surat').val(formatTimeForInput(ba.jam_surat));
            $('#zona_waktu_surat').val(ba.zona_waktu_surat);
            $('#lokasi_surat').val(ba.lokasi_surat);

            // LINK BA
            $('#link_ba').val(ba.link_modul_ba);
            $('#link_ba_hidden').val(ba.link_modul_ba);

            // Persetujuan - set langsung tanpa trigger change untuk menghindari konflik
            if (ba.m_persetujuan_id) {
                $('#persetujuan_id').val(ba.m_persetujuan_id);
            }
            console.log('Setting tanggal_persetujuan:', ba.tgl_persetujuan);
            $('#tanggal_persetujuan').val(formatDateForInput(ba.tgl_persetujuan));
            $('#nomor_persetujuan').val(ba.nomer_persetujuan);

            // Kapal Penerima Hibah
            console.log('Setting nama_kapal_penerima:', ba.kapal_code_temp);
            console.log('Field exists:', $('#nama_kapal_penerima').length > 0);
            $('#nama_kapal_penerima').val(ba.kapal_code_temp);
            console.log('Field value after set:', $('#nama_kapal_penerima').val());

            // Volume BBM
            $('#keterangan_jenis_bbm').val(ba.keterangan_jenis_bbm);
            $('#volume_sebelum').val(ba.volume_sebelum);
            $('#volume_pemakaian').val(ba.volume_pemakaian);
            $('#volume_sisa').val(ba.volume_sisa);
            $('#sebab_temp').val(ba.sebab_temp);

            // Staf Pangkalan
            $('#jabatan_staf_pangkalan').val(ba.jabatan_staf_pangkalan);
            $('#nama_petugas').val(ba.nama_staf_pagkalan);
            $('#nip_petugas').val(ba.nip_staf);
            $('#an_staf').prop('checked', ba.an_staf == 1);

            $('#nama_nahkoda').val(ba.nama_nahkoda);
            $('#pangkat_nahkoda').val(ba.pangkat_nahkoda);
            $('#nip_nahkoda').val(ba.nip_nahkoda);
            $('#nama_kkm').val(ba.nama_kkm);
            $('#nip_kkm').val(ba.nip_kkm);

            // Nakhoda & KKM Penerima Hibah
            $('#nama_nahkoda_penerima').val(ba.nama_nahkoda_temp);
            $('#pangkat_nahkoda_penerima').val(ba.pangkat_nahkoda_temp);
            $('#nip_nahkoda_penerima').val(ba.nip_nahkoda_temp);
            $('#nama_kkm_penerima').val(ba.nama_kkm_temp);
            $('#nip_kkm_penerima').val(ba.nip_kkm_temp);

            console.log('Setting checkboxes:', {
                an_staf: ba.an_staf
                , an_nakhoda: ba.an_nakhoda
                , an_kkm: ba.an_kkm
                , an_nakhoda_temp: ba.an_nakhoda_temp
                , an_kkm_temp: ba.an_kkm_temp
            });
            $('#an_nakhoda').prop('checked', ba.an_nakhoda == 1);
            $('#an_kkm').prop('checked', ba.an_kkm == 1);
            $('#an_nakhoda_penerima').prop('checked', ba.an_nakhoda_temp == 1);
            $('#an_kkm_penerima').prop('checked', ba.an_kkm_temp == 1);
        }

        // Test function untuk debug
        window.testEditForm = function() {
            const testData = {
                trans_id: 18
                , nomor_surat: "270/PSDKPLan.1-HIU12/PW.431/IX/2025112a"
                , kapal_code_temp: "TEST KAPAL"
                , nama_staf_pagkalan: "TEST STAF"
                , tgl_persetujuan: "2025-10-08"
                , m_persetujuan_id: 1
                , an_staf: 1
                , an_nakhoda: 1
                , keterangan_jenis_bbm: "BIO SOLAR"
                , volume_sebelum: "100"
                , volume_pemakaian: "50"
                , volume_sisa: "50"
                , sebab_temp: "Test reason"
                , nama_nahkoda: "Test Nakhoda"
                , pangkat_nahkoda: "Test Pangkat"
                , nip_nahkoda: "123456789"
                , nama_kkm: "Test KKM"
                , nip_kkm: "987654321"
                , nama_nahkoda_temp: "Test Nakhoda Temp"
                , pangkat_nahkoda_temp: "Test Pangkat Temp"
                , nip_nahkoda_temp: "111111111"
                , nama_kkm_temp: "Test KKM Temp"
                , nip_kkm_temp: "222222222"
                , an_kkm: 1
                , an_nakhoda_temp: 1
                , an_kkm_temp: 1
            };

            console.log('Testing with data:', testData);
            fillEditForm(testData);
        };

        window.deleteBa = function(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus BA ini?')) return;

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${id}`
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
            });
        };

        window.generatePdf = function(baId) {
            showSuccess('Sedang membuat PDF...');
            $.ajax({
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${baId}/pdf`
                , type: 'GET'
                , timeout: 30000
                , success: function(response) {
                    if (response.success) {
                        window.open(response.download_url, '_blank');
                        showSuccess('PDF berhasil dibuat');
                    } else {
                        showError(response.message);
                    }
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
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${currentBaId}/upload`
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
            });
        }

        window.openViewDocumentModal = function(id) {
            currentBaId = id;
            $.ajax({
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${id}/view-document`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        $('#documentViewer').html(response.html);
                        $('#viewDocumentModal').removeClass('hidden');
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal memuat dokumen');
                }
            });
        };

        function deleteDocument() {
            if (!currentBaId) {
                showError('ID BA tidak ditemukan');
                return;
            }

            $.ajax({
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${currentBaId}/delete-document`
                , type: 'DELETE'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#viewDocumentModal').addClass('hidden');
                        loadData(); // Refresh table
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal menghapus dokumen');
                }
            });
        }

        window.viewDocument = function(baId) {
            $.ajax({
                url: `/ba-pemberi-hibah-bbm-dengan-instansi-lain/${baId}/view-document`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        $('#documentViewer').html(`<div class="text-center"><div class="mb-4"><h4 class="text-lg font-medium mb-2">${response.filename}</h4></div><div class="border rounded-lg p-4"><iframe src="${response.file_url}" width="100%" height="600px" class="border-0 rounded-lg"></iframe></div><div class="mt-4"><a href="${response.file_url}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Buka di Tab Baru</a></div></div>`);
                        $('#viewDocumentModal').removeClass('hidden');
                        showSuccess('Dokumen berhasil dimuat');
                    } else {
                        showError(response.message);
                    }
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
<div id="baModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-white">Form Tambah BA Pemberi Hibah BBM Dengan Instansi Lain</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data berikut untuk membuat Berita Acara</p>
                    </div>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="baForm" class="mt-6 space-y-6">
                    @csrf
                    <input type="hidden" id="baId" name="ba_id">

                    <!-- Kapal -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kapal
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="kapal_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Kapal <span class="text-red-500">*</span>
                                </label>
                                <select id="kapal_id" name="kapal_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Pilih Kapal --</option>
                                    @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->m_kapal_id }}">{{ $kapal->nama_kapal }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="code_kapal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">KODE KAPAL</label>
                                <input type="text" id="code_kapal" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Informasi BA -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-green-900 dark:text-green-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            Informasi BA
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="alamat_upt" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">LOKASI UPT</label>
                                <textarea id="alamat_upt" rows="3" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed resize-none"></textarea>
                            </div>
                            <div>
                                <label for="lokasi_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    LOKASI KAPAL <span class="text-red-500">*</span>
                                </label>
                                <textarea id="lokasi_surat" name="lokasi_surat" rows="3" required placeholder="Lokasi kapal..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white resize-none"></textarea>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label for="tanggal_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    TANGGAL <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tanggal_surat" name="tanggal_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="jam_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    JAM <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="jam_surat" name="jam_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="zona_waktu_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Zona Waktu Surat <span class="text-red-500">*</span>
                                </label>
                                <select id="zona_waktu_surat" name="zona_waktu_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                                    <option value="WIB">WIB</option>
                                    <option value="WITA">WITA</option>
                                    <option value="WIT">WIT</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="link_ba" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">LINK BA</label>
                                <input type="text" id="link_ba" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                                <input type="hidden" id="link_ba_hidden" name="link_modul_ba">
                            </div>
                            <div>
                                <label for="nomor_surat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    NOMOR BA <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nomor_surat" name="nomor_surat" required placeholder="BA/001/KKP/2024" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Kapal Penerima Hibah -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Kapal Penerima Hibah
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kapal_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nama Kapal Penerima Hibah <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nama_kapal_penerima" name="nama_kapal_penerima" required placeholder="Nama kapal penerima hibah" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Berdasarkan Persetujuan -->
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
                                    Berdasarkan persetujuan <span class="text-red-500">*</span>
                                </label>
                                <select id="persetujuan_id" name="persetujuan_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">--Pilih--</option>
                                    @foreach($persetujuans as $persetujuan)
                                    <option value="{{ $persetujuan->id }}">{{ $persetujuan->deskripsi_persetujuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nomor_persetujuan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NOMOR PERSETUJUAN</label>
                                <input type="text" id="nomor_persetujuan" name="nomor_persetujuan" placeholder="Nomor persetujuan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="tanggal_persetujuan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">TANGGAL PERSETUJUAN</label>
                                <input name="tgl_persetujuan" type="date" id="tanggal_persetujuan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Informasi BBM -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-yellow-900 dark:text-yellow-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                            </svg>
                            Informasi BBM
                        </h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="keterangan_jenis_bbm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jenis BBM <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" value="BIO SOLAR" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="volume_sebelum" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">BBM Sebelum Pengisian</label>
                                <input type="number" id="volume_sebelum" name="volume_sebelum" step="0.01" min="0" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div>
                                <label for="volume_pemakaian" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Jumlah BBM <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="volume_pemakaian" name="volume_pemakaian" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="volume_sisa" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sisa BBM</label>
                                <input type="number" id="volume_sisa" name="volume_sisa" step="0.01" min="0" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div class="lg:col-span-2">
                                <label for="sebab_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Alasan Hibah BBM <span class="text-red-500">*</span>
                                </label>
                                <textarea id="sebab_temp" name="sebab_temp" rows="3" required placeholder="Alasan hibah BBM" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- PEJABAT/STAF UPT -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            PEJABAT/STAF UPT KAPAL PEMBERI HIBAH
                        </h4>
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="an_staf" name="an_staf" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="an_staf" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">An.</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="jabatan_staf_pangkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Staf Pangkalan</label>
                                <input type="text" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div>
                                <label for="nama_petugas" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NAMA PEJABAT/STAF UPT</label>
                                <input type="text" id="nama_petugas" name="nama_petugas" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div>
                                <label for="nip_petugas" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_petugas" name="nip_petugas" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- NAKHODA -->
                    <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-cyan-900 dark:text-cyan-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            NAKHODA KAPAL PEMBERI HIBAH
                        </h4>
                        <div class="mb-3">
                            <input type="checkbox" id="an_nakhoda" name="an_nakhoda" value="1" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                            <label for="an_nakhoda" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">An.</label>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="nama_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NAMA NAKHODA</label>
                                <input type="text" id="nama_nahkoda" name="nama_nahkoda" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div>
                                <label for="pangkat_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pangkat/Gol</label>
                                <input type="text" id="pangkat_nahkoda" name="pangkat_nahkoda" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div>
                                <label for="nip_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_nahkoda" name="nip_nahkoda" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- KKM -->
                    <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-teal-900 dark:text-teal-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            KKM KAPAL PEMBERI HIBAH
                        </h4>
                        <div class="mb-3">
                            <input type="checkbox" id="an_kkm" name="an_kkm" value="1" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                            <label for="an_kkm" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">An.</label>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NAMA KKM</label>
                                <input type="text" id="nama_kkm" name="nama_kkm" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                            <div>
                                <label for="nip_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_kkm" name="nip_kkm" readonly class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- NAKHODA PENERIMA HIBAH -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-green-900 dark:text-green-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            NAKHODA PENERIMA HIBAH
                        </h4>
                        <div class="mb-3">
                            <input type="checkbox" id="an_nakhoda_penerima" name="an_nakhoda_penerima" value="1" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="an_nakhoda_penerima" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">An.</label>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="nama_nahkoda_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NAMA NAKHODA PENERIMA HIBAH</label>
                                <input type="text" id="nama_nahkoda_penerima" name="nama_nahkoda_penerima" placeholder="Nama nakhoda penerima" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="pangkat_nahkoda_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pangkat/Gol</label>
                                <input type="text" id="pangkat_nahkoda_penerima" name="pangkat_nahkoda_penerima" placeholder="Pangkat/Gol" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="nip_nahkoda_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_nahkoda_penerima" name="nip_nahkoda_penerima" placeholder="NIP" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- KKM PENERIMA HIBAH -->
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-emerald-900 dark:text-emerald-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            KKM PENERIMA HIBAH
                        </h4>
                        <div class="mb-3">
                            <input type="checkbox" id="an_kkm_penerima" name="an_kkm_penerima" value="1" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                            <label for="an_kkm_penerima" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">An.</label>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kkm_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NAMA KKM PENERIMA HIBAH</label>
                                <input type="text" id="nama_kkm_penerima" name="nama_kkm_penerima" placeholder="Nama KKM penerima" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="nip_kkm_penerima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input type="text" id="nip_kkm_penerima" name="nip_kkm_penerima" placeholder="NIP" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                </form>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="cancelBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" form="baForm" id="submitBtn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-lg">
                        Simpan BA
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="viewBaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail BA Pemberi Hibah BBM Dengan Instansi Lain</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap Berita Acara Pemberi Hibah BBM Dengan Instansi Lain</p>
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

<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[99999] hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Dokumen</h3>
            <button id="closeUploadModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                <label class="block text-sm font-medium mb-2">Pilih Dokumen <span class="text-red-500">*</span></label>
                <input type="file" id="documentFile" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded-lg cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG (Max 10MB)</p>
            </div>
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                <button type="button" id="cancelUploadBtn" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">Upload</button>
            </div>
        </form>
    </div>
</div>

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
<div id="helpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 mt-10 mb-10 max-h-[90vh] overflow-y-auto help-modal-scroll">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan BA Pemberi Hibah BBM Dengan Instansi Lain</h3>
                <button id="closeHelpModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="space-y-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">Tentang BA Pemberi Hibah BBM Dengan Instansi Lain</h4>
                    <p class="text-blue-800 dark:text-blue-200 text-sm leading-relaxed">
                        Berita Acara Pemberi Hibah BBM Dengan Instansi Lain digunakan untuk mencatat pemberian hibah BBM kepada instansi lain di luar kapal pengawas.
                        Dokumen ini berisi informasi tentang instansi pemberi, instansi penerima, volume BBM yang dihibahkan, dan kondisi pemberian.
                    </p>
                </div>

                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Langkah-langkah Pengisian:</h4>

                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">1</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Isi Informasi Umum</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Lengkapi nomor surat, tanggal, jam, zona waktu, dan lokasi pembuatan BA.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">2</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Data Instansi Pemberi</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Isi informasi instansi yang memberikan hibah BBM, termasuk nama instansi dan alamat.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">3</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Data Instansi Penerima</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Isi informasi instansi penerima hibah BBM, termasuk nama instansi dan alamat.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">4</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Data Volume Hibah</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Isi volume BBM yang dihibahkan, jenis BBM, dan kondisi pemberian hibah.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">5</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Informasi Petugas</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Lengkapi data staf pangkalan, nahkoda, dan KKM. Centang checkbox jika sebagai an.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">6</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Upload Dokumen</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Upload dokumen pendukung jika diperlukan (opsional).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">Catatan Penting:</h4>
                    <ul class="text-yellow-800 dark:text-yellow-200 text-sm space-y-1">
                        <li>• Pastikan data instansi pemberi dan penerima akurat</li>
                        <li>• Semua field bertanda (*) wajib diisi</li>
                        <li>• Volume hibah harus sesuai dengan kapasitas yang tersedia</li>
                        <li>• Dokumen ini harus ditandatangani oleh pihak yang berwenang</li>
                        <li>• Pastikan koordinasi dengan instansi penerima telah dilakukan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
