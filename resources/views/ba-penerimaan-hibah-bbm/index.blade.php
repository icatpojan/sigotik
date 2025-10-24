@extends('layouts.dashboard')

@section('title', 'BA Penerimaan Hibah BBM')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">BA Penerimaan Hibah BBM</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola Berita Acara Penerimaan Hibah BBM</p>
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
                        @foreach($kapals as $k)
                        <option value="{{ $k->code_kapal }}">{{ $k->nama_kapal }}</option>
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
                    <button type="button" id="clearFilterBtn" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg flex items-center justify-center">
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase border border-gray-300">Nomor Surat & Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase border border-gray-300">Kapal Penerima</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase border border-gray-300">Instansi Pemberi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase border border-gray-300">Volume</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase border border-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody id="baTableBody" class="bg-white dark:bg-gray-800">
                </tbody>
            </table>
        </div>

        <div id="loadingIndicator" class="hidden items-center justify-center py-8">
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600">Memuat data...</span>
            </div>
        </div>

        <div id="paginationContainer" class="px-6 py-3 border-t border-gray-200">
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
        let currentPage = 1
            , perPage = 10
            , currentBaId = null
            , currentEditMode = false;

        loadData();
        setupEventHandlers();
        setDefaultDates();
        setupDatePickers();

        function formatDateForInput(d) {
            if (!d || /^\d{4}-\d{2}-\d{2}$/.test(d)) return d || '';
            try {
                const date = new Date(d);
                return isNaN(date.getTime()) ? '' : `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
            } catch (e) {
                return '';
            }
        }

        function formatTimeForInput(t) {
            if (!t) return '';
            if (/^\d{2}:\d{2}$/.test(t)) return t;
            if (/^\d{2}:\d{2}:\d{2}$/.test(t)) return t.substring(0, 5);
            return '';
        }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(...args), wait);
            };
        }

        function formatDate(d) {
            return !d ? '-' : new Date(d).toLocaleDateString('id-ID', {
                day: '2-digit'
                , month: '2-digit'
                , year: 'numeric'
            });
        }

        function formatNumber(n) {
            return (!n && n !== 0) ? '0' : new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 2
            }).format(n);
        }

        function showLoading() {
            $('#baTableBody').html('<tr><td colspan="5" class="px-6 py-4 text-center"><div class="flex items-center justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2">Memuat...</span></div></td></tr>');
        }

        function showSuccess(msg) {
            if (typeof toastr !== 'undefined') toastr.success(msg);
        }

        function showError(msg) {
            if (typeof toastr !== 'undefined') toastr.error(msg);
        }

        function setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            const currentTime = new Date().toTimeString().slice(0, 5); // Format HH:MM

            $('#tanggal_surat').val(today);
            if ($('#jam_surat').length) $('#jam_surat').val(currentTime);
        }

        function resetForm() {
            $('#baForm')[0].reset();
            $('#modalTitle').text('Form Tambah BA Penerimaan Hibah BBM');
            $('#submitBtn').html('Simpan BA');
            currentBaId = null;
            currentEditMode = false;
            clearKapalData();
            clearUptData();
            setDefaultDates();
            $('#an_staf, #an_nakhoda, #an_kkm').prop('checked', false);
            $('.is-invalid').removeClass('is-invalid');
            $('.text-red-500.text-sm').remove();

            // Reset transport items to 1 empty item
            $('.transport-item').remove();
            addTransportItem();
        }

        function displayValidationErrors(errors) {
            $('.is-invalid').removeClass('is-invalid');
            $('.text-red-500.text-sm').remove();
            Object.keys(errors).forEach(field => {
                const input = $(`[name="${field.includes('.') ? field.split('.')[0] : field}"]`);
                input.addClass('is-invalid');
                input.after(`<div class="text-red-500 text-sm mt-1">${errors[field][0]}</div>`);
            });
        }

        function loadData() {
            $.ajax({
                url: '{{ route("ba-penerimaan-hibah-bbm.data") }}'
                , type: 'GET'
                , data: {
                    page: currentPage
                    , per_page: perPage
                    , search: $('#search').val()
                    , kapal: $('#kapal').val()
                    , date_from: $('#date_from').val()
                    , date_to: $('#date_to').val()
                }
                , beforeSend: showLoading
                , success: function(response) {
                    if (response.success) {
                        renderTable(response.data);
                        renderPagination(response.pagination);
                    }
                }
            });
        }

        function loadKapalData(kapalId) {
            $.ajax({
                url: '{{ route("ba-penerimaan-hibah-bbm.kapal-data") }}'
                , type: 'GET'
                , data: {
                    kapal_id: kapalId
                }
                , success: function(response) {
                    if (response.success) {
                        const d = response.data;
                        $('#code_kapal').val(d.code_kapal);
                        $('#alamat_upt').val(d.alamat_upt);
                        $('#zona_waktu_surat').val(d.zona_waktu_upt);

                        // Hanya isi field personal jika tidak dalam edit mode
                        // Agar data dari BA tidak tertimpa
                        if (!currentEditMode) {
                            $('#nama_nahkoda').val(d.nama_nakoda);
                            $('#nip_nahkoda').val(d.nip_nakoda);
                            $('#nama_kkm').val(d.nama_kkm);
                            $('#nip_kkm').val(d.nip_kkm);
                            $('#nama_staf_pangkalan').val(d.nama_petugas);
                            $('#nip_staf').val(d.nip_petugas);
                            $('#jabatan_staf_pangkalan').val(d.jabatan_petugas);
                        }
                    } else {
                        clearKapalData();
                    }
                }
            });
        }

        function loadUptData(code) {
            $.ajax({
                url: '{{ route("ba-penerimaan-hibah-bbm.upt-data") }}'
                , type: 'GET'
                , data: {
                    code_upt: code
                }
                , success: function(response) {
                    if (response.success) {
                        $('#instansi_temp').val(response.data.nama);
                        $('#alamat_instansi_temp').val(response.data.alamat1);
                    }
                }
                , error: function(xhr) {
                    console.error('Error loading UPT data:', xhr);
                }
            });
        }

        function clearUptData() {
            $('#instansi_temp').val('');
            $('#alamat_instansi_temp').val('');
        }

        function loadBaData(kapalId) {
            const tanggalSurat = $('#tanggal_surat').val();

            if (!tanggalSurat || !kapalId) {
                return;
            }

            $.ajax({
                url: '{{ route("ba-penerimaan-hibah-bbm.ba-data") }}'
                , type: 'GET'
                , data: {
                    tanggal_surat: tanggalSurat
                    , m_kapal_id: kapalId
                }
                , success: function(response) {
                    if (response.jml > 0) {
                        $('#link_ba').val(response.nomor_surat);
                        $('#volume_sebelum').val(response.volume_sisa);
                    } else {
                        alert(response.message || 'Hari ini anda belum melakukan sounding/pengukuran');
                        $('#link_ba').val('');
                        $('#volume_sebelum').val('');
                    }
                }
                , error: function(xhr) {
                    console.error('Error loading BA data:', xhr);
                }
            });
        }

        function addTransportItem() {
            const transportCount = $('.transport-item').length + 1;
            const newItem = `
                <div class="transport-item border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Transportasi #${transportCount}</h5>
                        <button type="button" class="removeTransportBtn text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Transportasi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="transportasi[]" required placeholder="Contoh: Truk Tangki ABC-123" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Nomor DO <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_do[]" required placeholder="DO/001/2024" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Volume (Liter) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="volume_isi[]" step="0.01" min="0" required placeholder="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Keterangan</label>
                        <input type="text" name="keterangan[]" placeholder="Keterangan tambahan (opsional)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                    </div>
                </div>
            `;
            $('#transportList').append(newItem);
            updateTransportNumbering();
        }

        function updateTransportNumbering() {
            $('.transport-item').each(function(index) {
                $(this).find('h5').text(`Transportasi #${index + 1}`);

                // Show/hide remove button - minimal 1 item harus ada
                const removeBtn = $(this).find('.removeTransportBtn');
                if ($('.transport-item').length > 1) {
                    removeBtn.show();
                } else {
                    removeBtn.hide();
                }
            });
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

        function clearKapalData() {
            $('#code_kapal, #alamat_upt, #nama_staf_pangkalan, #nip_staf, #jabatan_staf_pangkalan, #nama_nahkoda, #nip_nahkoda, #nama_kkm, #nip_kkm, #link_ba, #volume_sebelum').val('');
            $('#an_staf, #an_nakhoda, #an_kkm').prop('checked', false);
        }

        function renderTable(data) {
            const tbody = $('#baTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5" class="px-6 py-4 text-center"><div class="flex flex-col items-center py-8"><svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><h3 class="text-lg font-medium mb-2">Tidak ada data</h3><button id="createFirstBaBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Tambah BA Pertama</button></div></td></tr>');
                return;
            }

            data.forEach(ba => {
                tbody.append(`
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 border border-gray-300">
                            <div class="font-medium">${ba.nomor_surat}</div>
                            <div class="text-gray-500 text-sm">${formatDate(ba.tanggal_surat)}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300">
                            <div class="font-medium">${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</div>
                            <div class="text-gray-500 text-sm">${ba.lokasi_surat || '-'}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300">
                            <div class="font-medium">${ba.instansi_temp || '-'}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300">
                            <div class="font-medium">${formatNumber(ba.volume_pengisian || 0)} L</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300">
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

        function renderPagination(p) {
            const c = $('#paginationContainer');
            c.empty();
            if (p.last_page <= 1) return;

            let html = `<div class="flex items-center justify-between mt-4"><div class="text-sm">Menampilkan ${p.from || 0} sampai ${p.to || 0} dari ${p.total} data</div><div class="flex space-x-1">`;

            if (p.current_page > 1) html += `<button onclick="changePage(${p.current_page - 1})" class="px-3 py-1 text-sm bg-white border rounded hover:bg-gray-50">Previous</button>`;

            for (let i = 1; i <= p.last_page; i++) {
                if (i === p.current_page) {
                    html += `<button class="px-3 py-1 text-sm bg-blue-600 text-white rounded">${i}</button>`;
                } else if (i === 1 || i === p.last_page || (i >= p.current_page - 1 && i <= p.current_page + 1)) {
                    html += `<button onclick="changePage(${i})" class="px-3 py-1 text-sm bg-white border rounded hover:bg-gray-50">${i}</button>`;
                } else if (i === p.current_page - 2 || i === p.current_page + 2) {
                    html += '<span class="px-2">...</span>';
                }
            }

            if (p.current_page < p.last_page) html += `<button onclick="changePage(${p.current_page + 1})" class="px-3 py-1 text-sm bg-white border rounded hover:bg-gray-50">Next</button>`;
            html += '</div></div>';
            c.html(html);
        }

        function setupEventHandlers() {
            // Help button
            $('#helpBtn').click(function() {
                $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
            });

            $(document).on('click', '#createBaBtn, #createFirstBaBtn', () => {
                resetForm();
                $('#baModal').removeClass('hidden');
                setTimeout(() => {
                    setupDatePickers();
                }, 100);
            });
            $('#closeModal, #cancelBtn').on('click', () => $('#baModal').addClass('hidden'));
            $('#closeViewModal, #closeViewModalBtn').on('click', () => $('#viewBaModal').addClass('hidden'));
            $('#closeUploadModal, #cancelUploadBtn').on('click', () => $('#uploadModal').addClass('hidden').removeClass('flex'));
            $('#closeViewDocumentModal').on('click', () => $('#viewDocumentModal').addClass('hidden').css('display', 'none'));

            // Close help modal
            $('#closeHelpModal').on('click', function() {
                $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
            });

            // Delete document button
            $('#deleteDocumentBtn').on('click', function() {
                if (currentBaId) {
                    deleteDocument(currentBaId);
                }
            });

            $('#kapal_id').on('change', function() {
                const kapalId = $(this).val();
                if (kapalId) {
                    loadKapalData(kapalId);
                    // Load BA data jika bukan mode edit
                    if (!currentEditMode) {
                        loadBaData(kapalId);
                    }
                } else {
                    clearKapalData();
                }
            });

            $('#tanggal_surat').on('change', function() {
                const kapalId = $('#kapal_id').val();
                if (kapalId && !currentEditMode) {
                    loadBaData(kapalId);
                }
            });

            $('#code_upt').on('change', function() {
                const code = $(this).val();
                if (code) loadUptData(code);
                else clearUptData();
            });

            // Add transport button
            $('#addTransportBtn').on('click', addTransportItem);

            // Remove transport button (delegated)
            $(document).on('click', '.removeTransportBtn', function() {
                if ($('.transport-item').length > 1) {
                    $(this).closest('.transport-item').remove();
                    updateTransportNumbering();
                }
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
            const url = currentEditMode ? `/ba-penerimaan-hibah-bbm/${currentBaId}` : '{{ route("ba-penerimaan-hibah-bbm.store") }}';
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
                        showError('Validasi gagal');
                    } else {
                        showError('Gagal menyimpan');
                    }
                }
            });
        }

        window.viewBa = function(id) {
            $.ajax({
                url: `/ba-penerimaan-hibah-bbm/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        const ba = response.data;
                        let html = `<div class="grid grid-cols-2 gap-4">
                            <div><strong>Nomor:</strong> ${ba.nomor_surat}</div>
                            <div><strong>Tanggal:</strong> ${formatDate(ba.tanggal_surat)}</div>
                            <div><strong>Kapal:</strong> ${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</div>
                            <div><strong>Instansi Pemberi:</strong> ${ba.instansi_temp || '-'}</div>
                            <div><strong>Volume:</strong> ${formatNumber(ba.volume_pengisian || 0)} L</div>
                            <div><strong>BBM:</strong> ${ba.keterangan_jenis_bbm}</div>
                        </div>`;
                        $('#viewBaContent').html(html);
                        $('#viewBaModal').removeClass('hidden');
                    }
                }
            });
        };

        window.editBa = function(id) {
            currentBaId = id;
            currentEditMode = true;
            $.ajax({
                url: `/ba-penerimaan-hibah-bbm/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        const ba = response.data;

                        // 1. Load data kapal dulu (tanpa mengisi field personal)
                        if (ba.kapal && ba.kapal.m_kapal_id) {
                            loadKapalData(ba.kapal.m_kapal_id);
                        }

                        // 2. Setelah kapal data loaded, baru isi data dari BA
                        setTimeout(() => {
                            fillEditForm(ba);
                            $('#modalTitle').text('Form Edit BA');
                            $('#submitBtn').html('Update');
                            $('#baModal').removeClass('hidden');
                            setTimeout(() => {
                                setupDatePickers();
                            }, 100);
                        }, 200);
                    }
                }
            });
        };

        function fillEditForm(ba) {
            console.log('Data BA yang diterima:', ba);
            console.log('instansi_temp:', ba.instansi_temp);
            console.log('alamat_instansi_temp:', ba.alamat_instansi_temp);

            $('#baId').val(ba.trans_id);
            $('#kapal_id').val(ba.kapal ? ba.kapal.m_kapal_id : '');
            $('#nomor_surat').val(ba.nomor_surat);
            $('#tanggal_surat').val(formatDateForInput(ba.tanggal_surat));
            $('#jam_surat').val(formatTimeForInput(ba.jam_surat));
            $('#zona_waktu_surat').val(ba.zona_waktu_surat);
            $('#lokasi_surat').val(ba.lokasi_surat);
            $('#link_ba').val(ba.link_modul_ba);
            $('#volume_sebelum').val(ba.volume_sebelum);
            $('#code_upt').val(ba.instansi_temp || '');
            $('#instansi_temp').val(ba.instansi_temp);
            $('#alamat_instansi_temp').val(ba.alamat_instansi_temp);
            $('#penyedia').val(ba.penyedia);
            $('#keterangan_jenis_bbm').val(ba.keterangan_jenis_bbm);
            $('#no_so').val(ba.no_so);
            $('#nama_penyedia').val(ba.nama_penyedia);
            $('#nama_staf_pangkalan').val(ba.nama_staf_pagkalan);
            $('#nip_staf').val(ba.nip_staf);
            $('#jabatan_staf_pangkalan').val(ba.jabatan_staf_pangkalan);
            $('#nama_nahkoda').val(ba.nama_nahkoda);
            $('#nip_nahkoda').val(ba.nip_nahkoda);
            $('#nama_kkm').val(ba.nama_kkm);
            $('#nip_kkm').val(ba.nip_kkm);
            $('#an_staf').prop('checked', ba.an_staf == 1);
            $('#an_nakhoda').prop('checked', ba.an_nakhoda == 1);
            $('#an_kkm').prop('checked', ba.an_kkm == 1);

            // Load transport details
            $('.transport-item').remove();
            if (ba.transdetails && ba.transdetails.length > 0) {
                ba.transdetails.forEach((detail, index) => {
                    if (index === 0) {
                        // Update first transport item
                        addTransportItem();
                        const firstItem = $('.transport-item').first();
                        firstItem.find('input[name="transportasi[]"]').val(detail.transportasi || '');
                        firstItem.find('input[name="no_do[]"]').val(detail.no_do || '');
                        firstItem.find('input[name="volume_isi[]"]').val(detail.volume_isi || '');
                        firstItem.find('input[name="keterangan[]"]').val(detail.keterangan || '');
                    } else {
                        // Add new transport item
                        addTransportItem();
                        const lastItem = $('.transport-item').last();
                        lastItem.find('input[name="transportasi[]"]').val(detail.transportasi || '');
                        lastItem.find('input[name="no_do[]"]').val(detail.no_do || '');
                        lastItem.find('input[name="volume_isi[]"]').val(detail.volume_isi || '');
                        lastItem.find('input[name="keterangan[]"]').val(detail.keterangan || '');
                    }
                });
                updateTransportNumbering();
            } else {
                // Add 1 empty item if no details
                addTransportItem();
            }
        }

        window.deleteBa = function(id) {
            if (!confirm('Yakin hapus?')) return;
            $.ajax({
                url: `/ba-penerimaan-hibah-bbm/${id}`
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
                    }
                }
            });
        };

        window.generatePdf = function(baId) {
            showSuccess('Membuat PDF...');
            window.open(`/ba-penerimaan-hibah-bbm/${baId}/pdf`, '_blank');
        };

        window.openUploadModal = function(baId) {
            currentBaId = baId;
            $('#uploadModal').removeClass('hidden').addClass('flex');
            $('#uploadForm')[0].reset();
        };

        function uploadDocument() {
            const formData = new FormData($('#uploadForm')[0]);
            $.ajax({
                url: `/ba-penerimaan-hibah-bbm/${currentBaId}/upload`
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
                    }
                }
            });
        }

        window.viewDocument = function(baId) {
            currentBaId = baId;
            $.ajax({
                url: `/ba-penerimaan-hibah-bbm/${baId}/view-document`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        $('#documentViewer').html(`<div class="text-center"><h4 class="text-lg mb-4">${response.filename}</h4><iframe src="${response.file_url}" width="100%" height="600px" class="border rounded"></iframe><div class="mt-4"><a href="${response.file_url}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded inline-flex items-center">Buka Tab Baru</a></div></div>`);
                        $('#viewDocumentModal').removeClass('hidden').css('display', 'flex');
                    }
                }
            });
        };

        function deleteDocument(baId) {
            if (!confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                return;
            }

            $.ajax({
                url: `/ba-penerimaan-hibah-bbm/${baId}/delete-document`
                , type: 'DELETE'
                , data: {
                    _token: '{{ csrf_token() }}'
                }
                , success: function(response) {
                    if (response.success) {
                        showSuccess('Dokumen berhasil dihapus');
                        $('#viewDocumentModal').addClass('hidden').css('display', 'none');
                        loadData();
                    } else {
                        showError(response.message);
                    }
                }
                , error: function(xhr) {
                    showError('Gagal menghapus dokumen');
                }
            });
        }

        window.changePage = function(page) {
            currentPage = page;
            loadData();
        };
    });

</script>
@endsection

@section('modals')
<div id="baModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[99999]">

    <div class="flex items-center justify-center min-h-full py-8 px-4">
        <div class="relative mx-auto p-6 border w-full max-w-6xl shadow-lg rounded-lg bg-white dark:bg-gray-800 my-8">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b">
                    <div>
                        <h3 id="modalTitle" class="text-xl font-semibold">Form Tambah BA Penerimaan Hibah BBM</h3>
                        <p class="text-sm text-gray-600 mt-1">Lengkapi data berikut</p>
                    </div>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-6 max-h-[70vh] overflow-y-auto pr-2">
                    <form id="baForm" class="space-y-6">
                        @csrf
                        <input type="hidden" id="baId">

                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Kapal Penerima
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="kapal_id" class="block text-sm font-semibold mb-2">Pilih Kapal <span class="text-red-500">*</span></label>
                                    <select id="kapal_id" name="kapal_id" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">-- Pilih --</option>
                                        @foreach($kapals as $kapal)
                                        <option value="{{ $kapal->m_kapal_id }}">{{ $kapal->nama_kapal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="code_kapal" class="block text-sm font-semibold mb-2">Kode Kapal</label>
                                    <input type="text" id="code_kapal" readonly class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-green-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                Lokasi & Waktu
                            </h4>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="alamat_upt" class="block text-sm font-semibold mb-2">Alamat UPT</label>
                                    <textarea id="alamat_upt" rows="3" readonly class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed resize-none"></textarea>
                                </div>
                                <div>
                                    <label for="lokasi_surat" class="block text-sm font-semibold mb-2">Lokasi BA <span class="text-red-500">*</span></label>
                                    <textarea id="lokasi_surat" name="lokasi_surat" rows="3" required placeholder="Lokasi..." class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white resize-none"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-2">
                                    <label for="nomor_surat" class="block text-sm font-semibold mb-2">Nomor BA <span class="text-red-500">*</span></label>
                                    <input type="text" id="nomor_surat" name="nomor_surat" required placeholder="BA/001/KKP/2024" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label for="tanggal_surat" class="block text-sm font-semibold mb-2">Tanggal <span class="text-red-500">*</span></label>
                                    <input type="date" id="tanggal_surat" name="tanggal_surat" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label for="jam_surat" class="block text-sm font-semibold mb-2">Jam <span class="text-red-500">*</span></label>
                                    <input type="time" id="jam_surat" name="jam_surat" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="zona_waktu_surat" class="block text-sm font-semibold mb-2">Zona Waktu <span class="text-red-500">*</span></label>
                                <select id="zona_waktu_surat" name="zona_waktu_surat" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                                    <option value="WIB">WIB</option>
                                    <option value="WITA">WITA</option>
                                    <option value="WIT">WIT</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-orange-900 dark:text-orange-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Link BA Sebelum Pengisian
                            </h4>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label for="link_ba" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Link BA <span class="text-red-500">*</span></label>
                                    <input type="text" id="link_ba" name="link_ba" readonly required class="w-full px-4 py-3 border rounded-lg bg-gray-100 dark:bg-gray-600 cursor-not-allowed">
                                </div>
                                <div>
                                    <label for="volume_sebelum" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">BBM Sebelum Pengisian (Liter) <span class="text-red-500">*</span></label>
                                    <input type="number" id="volume_sebelum" name="volume_sebelum" step="0.01" min="0" required readonly class="w-full px-4 py-3 border rounded-lg bg-gray-100 dark:bg-gray-600 cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-green-900 dark:text-green-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Instansi Pemberi
                            </h4>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <div>
                                    <label for="code_upt" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih Instansi <span class="text-red-500">*</span></label>
                                    <select id="code_upt" name="code_upt" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                                        <option value="">-- Pilih --</option>
                                        @foreach($upts as $u)
                                        <option value="{{ $u->code }}">{{ $u->nama }}</option>
                                        @endforeach
                                        <option value="999">INSTANSI LAINNYA</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="instansi_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Instansi <span class="text-red-500">*</span></label>
                                    <input type="text" id="instansi_temp" name="instansi_temp" required placeholder="Nama instansi pemberi" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>
                                <div>
                                    <label for="alamat_instansi_temp" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alamat Instansi</label>
                                    <textarea id="alamat_instansi_temp" name="alamat_instansi_temp" rows="3" placeholder="Alamat instansi pemberi" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-yellow-900 dark:text-yellow-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                </svg>
                                Info BBM & Penyedia
                            </h4>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label for="penyedia" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Penyedia</label>
                                    <input type="text" id="penyedia" name="penyedia" placeholder="Nama penyedia" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>
                                <div>
                                    <label for="keterangan_jenis_bbm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jenis BBM <span class="text-red-500">*</span></label>
                                    <input type="text" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" value="BIO SOLAR" required placeholder="Jenis BBM" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>
                                <div>
                                    <label for="no_so" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">No. SO</label>
                                    <input type="text" id="no_so" name="no_so" placeholder="Nomor SO" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <h5 class="text-md font-medium text-yellow-900 dark:text-yellow-100 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Detail Transportasi BBM <span class="text-red-500 ml-1">*</span>
                                </h5>
                                <button type="button" id="addTransportBtn" class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-600 bg-yellow-100 hover:bg-yellow-200 border border-yellow-300 hover:border-yellow-400 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Transportasi
                                </button>
                            </div>

                            <div id="transportList" class="space-y-4">
                                <!-- Transportasi pertama -->
                                <div class="transport-item border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                                    <div class="flex items-center justify-between mb-3">
                                        <h5 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Transportasi #1</h5>
                                        <button type="button" class="removeTransportBtn text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" style="display: none;">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Transportasi <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="transportasi[]" required placeholder="Contoh: Truk Tangki ABC-123" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Nomor DO <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="no_do[]" required placeholder="DO/001/2024" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Volume (Liter) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" name="volume_isi[]" step="0.01" min="0" required placeholder="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Keterangan</label>
                                        <input type="text" name="keterangan[]" placeholder="Keterangan tambahan (opsional)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="nama_penyedia" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Petugas Penyedia</label>
                                <input type="text" id="nama_penyedia" name="nama_penyedia" placeholder="Nama petugas penyedia" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
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
                                    <label for="jabatan_staf_pangkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jabatan Staf</label>
                                    <input type="text" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" placeholder="Jabatan staf pangkalan" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>
                                <div>
                                    <label for="nip_staf" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP Staf</label>
                                    <input type="text" id="nip_staf" name="nip_staf" placeholder="Nomor Induk Pegawai" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>
                            </div>
                        </div>

                        <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-cyan-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Nakhoda & KKM Kapal
                            </h4>
                            <div class="mb-3">
                                <input type="checkbox" id="an_nakhoda" name="an_nakhoda" value="1" class="h-4 w-4">
                                <label for="an_nakhoda" class="ml-2 text-sm">An. Nakhoda</label>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="nama_nahkoda" class="block text-sm font-semibold mb-2">Nama</label>
                                    <input type="text" id="nama_nahkoda" name="nama_nahkoda" placeholder="Nama nakhoda" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-cyan-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label for="nip_nahkoda" class="block text-sm font-semibold mb-2">NIP</label>
                                    <input type="text" id="nip_nahkoda" name="nip_nahkoda" placeholder="NIP" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-cyan-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="checkbox" id="an_kkm" name="an_kkm" value="1" class="h-4 w-4">
                                <label for="an_kkm" class="ml-2 text-sm">An. KKM</label>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="nama_kkm" class="block text-sm font-semibold mb-2">Nama</label>
                                    <input type="text" id="nama_kkm" name="nama_kkm" placeholder="Nama KKM" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-cyan-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label for="nip_kkm" class="block text-sm font-semibold mb-2">NIP</label>
                                    <input type="text" id="nip_kkm" name="nip_kkm" placeholder="NIP" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-cyan-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="cancelBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg transition-colors">Batal</button>
                    <button type="submit" form="baForm" id="submitBtn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm hover:shadow-md transition-all">Simpan BA</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="viewBaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-3/4 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b">
                    <h3 class="text-xl font-semibold">Detail BA</h3>
                    <button id="closeViewModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="viewBaContent" class="mt-6"></div>
                <div class="flex justify-end pt-6 border-t">
                    <button id="closeViewModalBtn" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-[99999] hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-lg font-semibold">Upload Dokumen</h3>
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
                <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:bg-green-50 file:text-green-700 border rounded-lg cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">PDF, DOC, JPG, PNG (Max 10MB)</p>
            </div>
            <div class="flex justify-end space-x-3 p-6 border-t">
                <button type="button" id="cancelUploadBtn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Upload</button>
            </div>
        </form>
    </div>
</div>

<div id="viewDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-[99999] hidden" style="display: none;">
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
            <div id="documentViewer"></div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div id="helpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 mt-10 mb-10 max-h-[90vh] overflow-y-auto help-modal-scroll">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan BA Penerimaan Hibah BBM</h3>
                <button id="closeHelpModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="space-y-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">Tentang BA Penerimaan Hibah BBM</h4>
                    <p class="text-blue-800 dark:text-blue-200 text-sm leading-relaxed">
                        Berita Acara Penerimaan Hibah BBM digunakan untuk mencatat penerimaan hibah BBM dari berbagai sumber.
                        Dokumen ini berisi informasi tentang sumber hibah, volume BBM yang diterima, dan kondisi penerimaan.
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
                                <h5 class="font-medium text-gray-900 dark:text-white">Data Sumber Hibah</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Isi informasi sumber hibah BBM, termasuk nama instansi atau kapal pemberi hibah.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">3</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Data Volume Hibah</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Isi volume BBM yang diterima, jenis BBM, dan kondisi penerimaan hibah.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">4</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Informasi Petugas</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Lengkapi data staf pangkalan, nahkoda, dan KKM. Centang checkbox jika sebagai an.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">5</span>
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
                        <li>• Pastikan data sumber hibah akurat</li>
                        <li>• Semua field bertanda (*) wajib diisi</li>
                        <li>• Volume hibah harus sesuai dengan kapasitas yang tersedia</li>
                        <li>• Dokumen ini harus ditandatangani oleh pihak yang berwenang</li>
                        <li>• Pastikan koordinasi dengan sumber hibah telah dilakukan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
