@extends('layouts.dashboard')

@section('title', 'BA Sesudah Pelayaran')

<!-- Toastr CSS and JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">BA Sesudah Pelayaran</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola Berita Acara Sesudah Pelayaran</p>
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
                    <button type="button" id="clearFilter" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Volume Sisa</th>
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let currentPage = 1;
    let currentFilters = {};

    $(document).ready(function() {
        // Load initial data
        loadBaData();

        // Help button
        $('#helpBtn').click(function() {
            $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        // Help modal controls
        $('#closeHelpModal').click(function() {
            $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        // Modal controls
        $('#createBaBtn').click(function() {
            $('#modalTitle').text('Form Tambah BA Sesudah Pelayaran');
            $('#baForm')[0].reset();
            $('#baId').val('');

            // Set default values
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            const time = now.toTimeString().slice(0, 5);

            $('#tanggal_surat').val(today);
            $('#jam_surat').val(time);
            $('#zona_waktu_surat').val('WIB');

            $('#baModal').removeClass('hidden');

            // Focus on first input
            setTimeout(() => {
                $('#kapal_id').focus();
            }, 100);
        });

        $('#closeModal, #cancelBtn').click(function() {
            $('#baModal').addClass('hidden');
        });

        // Event delegation for dynamically created buttons
        $(document).on('click', '#createFirstBaBtn', function() {
            $('#modalTitle').text('Form Tambah BA Sesudah Pelayaran');
            $('#baForm')[0].reset();
            $('#baId').val('');

            // Set default values
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            const time = now.toTimeString().slice(0, 5);

            $('#tanggal_surat').val(today);
            $('#jam_surat').val(time);
            $('#zona_waktu_surat').val('WIB');

            $('#baModal').removeClass('hidden');

            // Focus on first input
            setTimeout(() => {
                $('#kapal_id').focus();
            }, 100);
        });

        // ESC key to close modal
        $(document).keydown(function(e) {
            if (e.keyCode === 27) { // ESC key
                $('#baModal').addClass('hidden');
                $('#viewBaModal').addClass('hidden');
            }
        });

        // Auto-format volume input
        $('#volume_sisa').on('input', function() {
            let value = $(this).val();
            // Remove non-numeric characters except decimal point
            value = value.replace(/[^0-9.]/g, '');
            // Ensure only one decimal point
            if ((value.match(/\./g) || []).length > 1) {
                value = value.substring(0, value.lastIndexOf('.'));
            }
            // Limit to 2 decimal places
            if (value.indexOf('.') !== -1) {
                value = value.substring(0, value.indexOf('.') + 3);
            }
            $(this).val(value);
        });

        // Auto-format NIP inputs
        $('input[name*="nip"]').on('input', function() {
            let value = $(this).val();
            // Remove non-numeric characters
            value = value.replace(/[^0-9]/g, '');
            // Limit to 18 digits
            if (value.length > 18) {
                value = value.substring(0, 18);
            }
            $(this).val(value);
        });

        // Auto-format nama inputs (capitalize first letter of each word)
        $('input[name*="nama"]').on('input', function() {
            let value = $(this).val();
            // Capitalize first letter of each word
            value = value.replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
            $(this).val(value);
        });

        // Auto-format jabatan input (capitalize first letter of each word)
        $('#jabatan_staf_pangkalan').on('input', function() {
            let value = $(this).val();
            // Capitalize first letter of each word
            value = value.replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
            $(this).val(value);
        });

        // Handle date input validation
        $('#tanggal_surat').on('change', function() {
            const dateValue = $(this).val();
            if (dateValue) {
                const selectedDate = new Date(dateValue);
                const today = new Date();

                // Check if date is in the future
                if (selectedDate > today) {
                    showNotification('warning', 'Tanggal tidak boleh lebih dari hari ini');
                    $(this).val('');
                }
            }
        });

        // Set max date to today and initialize date input
        $(document).ready(function() {
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_surat').attr('max', today);

            // Force date input to work properly
            $('#tanggal_surat').on('focus', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add click handler for date input
            $('#tanggal_surat').on('click', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add touch handler for mobile
            $('#tanggal_surat').on('touchstart', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add keyboard handler
            $('#tanggal_surat').on('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (this.showPicker) {
                        this.showPicker();
                    }
                }
            });

            // Add double-click handler
            $('#tanggal_surat').on('dblclick', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add mousedown handler
            $('#tanggal_surat').on('mousedown', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add showPicker functionality for filter date inputs
            $('#date_from, #date_to').on('click', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add double-click handler for filter date inputs
            $('#date_from, #date_to').on('dblclick', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add mousedown handler for filter date inputs
            $('#date_from, #date_to').on('mousedown', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add focus handler for filter date inputs
            $('#date_from, #date_to').on('focus', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });

            // Add keydown handler for Enter key
            $('#date_from, #date_to').on('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (this.showPicker) {
                        this.showPicker();
                    }
                }
            });
        });

        // Handle checkbox changes for better UX
        $('input[type="checkbox"]').on('change', function() {
            const checkbox = $(this);
            const label = checkbox.next('label');
            const hiddenInput = $(`input[name="${checkbox.attr('id')}"]`);

            if (checkbox.is(':checked')) {
                label.addClass('text-blue-600 dark:text-blue-400');
                hiddenInput.val('1');
            } else {
                label.removeClass('text-blue-600 dark:text-blue-400');
                hiddenInput.val('0');
            }
        });

        // Initialize checkbox states on page load
        $('input[type="checkbox"]').each(function() {
            const checkbox = $(this);
            const label = checkbox.next('label');

            if (checkbox.is(':checked')) {
                label.addClass('text-blue-600 dark:text-blue-400');
            }
        });

        // Reset checkbox states when form is reset
        $('#baForm').on('reset', function() {
            $('input[type="checkbox"]').each(function() {
                const checkbox = $(this);
                const label = checkbox.next('label');
                const hiddenInput = $(`input[name="${checkbox.attr('id')}"]`);

                label.removeClass('text-blue-600 dark:text-blue-400');
                hiddenInput.val('0');
            });
        });


        $('#closeViewModal').click(function() {
            $('#viewBaModal').addClass('hidden');
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1;
            loadBaData();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#kapal').val('');
            $('#date_from').val('');
            $('#date_to').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadBaData();
            showNotification('info', 'Filter telah direset');
        });

        // Kapal selection change - auto-fill data
        $('#kapal_id').change(function() {
            const kapalId = $(this).val();
            if (kapalId) {
                // Show loading state
                const kapalSelect = $(this);
                const originalValue = kapalSelect.val();
                kapalSelect.prop('disabled', true);

                $.ajax({
                    url: '/ba-sesudah-pelayaran/kapal-data'
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
                            $('#jabatan_staf_pangkalan').val(data.jabatan_petugas);
                            $('#nama_staf_pagkalan').val(data.nama_petugas);
                            $('#nip_staf').val(data.nip_petugas);
                            $('#nama_nahkoda').val(data.nama_nakoda);
                            $('#nip_nahkoda').val(data.nip_nakoda);
                            $('#nama_kkm').val(data.nama_kkm);
                            $('#nip_kkm').val(data.nip_kkm);

                            showNotification('success', 'Data kapal berhasil dimuat');
                        }
                    }
                    , error: function() {
                        showNotification('error', 'Gagal memuat data kapal');
                        kapalSelect.val(''); // Reset selection
                    }
                    , complete: function() {
                        kapalSelect.prop('disabled', false);
                    }
                });
            } else {
                // Clear all auto-filled data
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
            }
        });

        // BA form submission
        $('#baForm').on('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const requiredFields = ['kapal_id', 'nomor_surat', 'tanggal_surat', 'jam_surat', 'lokasi_surat', 'volume_sisa'];
            let isValid = true;
            let errorMessage = '';

            requiredFields.forEach(function(field) {
                const element = $(`#${field}`);
                if (!element.val() || element.val().trim() === '') {
                    isValid = false;
                    element.addClass('border-red-500');
                    errorMessage += `- ${element.prev('label').text().replace('*', '').trim()} harus diisi\n`;
                } else {
                    element.removeClass('border-red-500');
                }
            });

            // Validate volume_sisa is positive number
            const volumeSisa = parseFloat($('#volume_sisa').val());
            if (isNaN(volumeSisa) || volumeSisa < 0) {
                isValid = false;
                $('#volume_sisa').addClass('border-red-500');
                errorMessage += '- Volume BBM harus berupa angka positif\n';
            } else {
                $('#volume_sisa').removeClass('border-red-500');
            }

            if (!isValid) {
                showNotification('error', 'Validasi gagal:\n' + errorMessage);
                return;
            }

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html(`
                <span class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                </span>
            `);

            // Handle checkbox values properly - update hidden inputs BEFORE creating FormData
            $('input[name="an_staf"]').val($('#an_staf').is(':checked') ? '1' : '0');
            $('input[name="an_nakhoda"]').val($('#an_nakhoda').is(':checked') ? '1' : '0');
            $('input[name="an_kkm"]').val($('#an_kkm').is(':checked') ? '1' : '0');

            const formData = new FormData(this);
            const baId = $('#baId').val();
            const url = baId ? `/ba-sesudah-pelayaran/${baId}` : '/ba-sesudah-pelayaran';
            const method = baId ? 'PUT' : 'POST';

            if (baId) {
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
                        $('#baModal').addClass('hidden');
                        loadBaData();
                        showNotification('success', response.message);
                    } else {
                        showNotification('error', response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validasi gagal:\n';
                        for (let field in errors) {
                            errorMessage += `- ${errors[field][0]}\n`;
                            $(`#${field}`).addClass('border-red-500');
                        }
                        showNotification('error', errorMessage);
                    } else {
                        showNotification('error', 'Terjadi kesalahan saat menyimpan data');
                    }
                }
                , complete: function() {
                    // Reset button state
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    // Function to load BA data via AJAX
    function loadBaData(page = 1) {
        currentPage = page;

        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#baTableBody').html('');
        $('#paginationContainer').html('');

        const filters = {
            search: $('#search').val()
            , kapal: $('#kapal').val()
            , date_from: $('#date_from').val()
            , date_to: $('#date_to').val()
            , per_page: $('#perPage').val()
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/ba-sesudah-pelayaran/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderBaTable(response.data);
                    renderPagination(response.pagination);
                } else {
                    showErrorMessage('Gagal memuat data BA');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
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

    // Function to render BA table
    function renderBaTable(baData) {
        const tbody = $('#baTableBody');
        tbody.html('');

        if (baData.length === 0) {
            tbody.html(`
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

        baData.forEach(function(ba, index) {
            const tanggalSurat = new Date(ba.tanggal_surat).toLocaleDateString('id-ID');

            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">Nomor : ${ba.nomor_surat}</div>
                            <div class="text-gray-500 dark:text-gray-400">Tanggal : ${tanggalSurat}</div>
                            <div class="text-gray-500 dark:text-gray-400">Jam : ${ba.jam_surat} ${ba.zona_waktu_surat}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">Kapal : ${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code}</div>
                            <div class="text-gray-500 dark:text-gray-400">Lokasi : ${ba.lokasi_surat}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${ba.volume_sisa} Liter</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewBa(${ba.trans_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="editBa(${ba.trans_id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit BA">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteBa(${ba.trans_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus BA">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <button onclick="generatePdf(${ba.trans_id})" class="p-2 text-purple-600 bg-purple-50 hover:bg-purple-100 border border-purple-200 hover:border-purple-300 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50 dark:border-purple-700 dark:hover:border-purple-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Generate PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
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
        container.html('');

        if (pagination.last_page <= 1) return;

        let paginationHtml = '<div class="flex items-center justify-between">';

        // Info
        paginationHtml += `<div class="text-sm text-gray-700 dark:text-gray-300">
        Menampilkan ${pagination.from || 0} sampai ${pagination.to || 0} dari ${pagination.total} data
    </div>`;

        // Pagination buttons
        paginationHtml += '<div class="flex space-x-1">';

        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += `<button onclick="loadBaData(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700">
            Previous
        </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadBaData(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-blue-600 bg-blue-50 border-blue-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50'} border hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700">
            ${i}
        </button>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadBaData(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700">
            Next
        </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    // Helper functions

    // Action functions
    function viewBa(baId) {
        $.ajax({
            url: `/ba-sesudah-pelayaran/${baId}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const ba = response.data;
                    const tanggalFormatted = new Date(ba.tanggal_surat).toLocaleDateString('id-ID', {
                        weekday: 'long'
                        , year: 'numeric'
                        , month: 'long'
                        , day: 'numeric'
                    });

                    const content = `
                        <div class="space-y-6">
                            <!-- Informasi Umum -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Informasi Umum
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nomor BA</label>
                                        <p class="text-gray-900 dark:text-white font-medium">${ba.nomor_surat || '-'}</p>
                                </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal & Waktu</label>
                                        <p class="text-gray-900 dark:text-white">${tanggalFormatted}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">${ba.jam_surat} ${ba.zona_waktu_surat}</p>
                            </div>
                            <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kapal</label>
                                        <p class="text-gray-900 dark:text-white font-medium">${ba.kapal ? ba.kapal.nama_kapal : ba.kapal_code || '-'}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Kode: ${ba.kapal ? ba.kapal.code_kapal : ba.kapal_code || '-'}</p>
                            </div>
                                </div>
                            </div>

                            <!-- Lokasi dan BBM -->
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-green-900 dark:text-green-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Lokasi dan BBM
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Lokasi Pembuatan BA</label>
                                        <p class="text-gray-900 dark:text-white">${ba.lokasi_surat || '-'}</p>
                            </div>
                            <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Volume Sisa BBM</label>
                                        <p class="text-gray-900 dark:text-white font-medium text-lg">${ba.volume_sisa ? parseFloat(ba.volume_sisa).toLocaleString('id-ID') : '0'} Liter</p>
                            </div>
                                </div>
                            </div>

                            <!-- Pejabat UPT -->
                            ${ba.jabatan_staf_pangkalan || ba.nama_staf_pagkalan || ba.nip_staf ? `
                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Pejabat/Staf UPT (Menyaksikan)
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Jabatan</label>
                                        <p class="text-gray-900 dark:text-white">${(ba.an_staf == 1 || ba.an_staf === true) ? 'An. ' : ''}${ba.jabatan_staf_pangkalan || '-'}</p>
                            </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama</label>
                                        <p class="text-gray-900 dark:text-white">${ba.nama_staf_pagkalan || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">NIP</label>
                                        <p class="text-gray-900 dark:text-white">${ba.nip_staf || '-'}</p>
                                    </div>
                                </div>
                            </div>
                            ` : ''}

                            <!-- Nakhoda -->
                            ${ba.nama_nahkoda || ba.nip_nahkoda ? `
                            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    Nakhoda Kapal
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama</label>
                                        <p class="text-gray-900 dark:text-white">${(ba.an_nakhoda == 1 || ba.an_nakhoda === true) ? 'An. ' : ''}${ba.nama_nahkoda || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">NIP</label>
                                        <p class="text-gray-900 dark:text-white">${ba.nip_nahkoda || '-'}</p>
                                    </div>
                                </div>
                            </div>
                            ` : ''}

                            <!-- KKM -->
                            ${ba.nama_kkm || ba.nip_kkm ? `
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-red-900 dark:text-red-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    KKM (Kepala Kamar Mesin)
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama</label>
                                        <p class="text-gray-900 dark:text-white">${(ba.an_kkm == 1 || ba.an_kkm === true) ? 'An. ' : ''}${ba.nama_kkm || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">NIP</label>
                                        <p class="text-gray-900 dark:text-white">${ba.nip_kkm || '-'}</p>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `;
                    $('#viewBaContent').html(content);
                    $('#viewBaModal').removeClass('hidden');
                }
            }
            , error: function(xhr, status, error) {
                showNotification('error', 'Gagal memuat data BA');
            }
        });
    }

    function editBa(baId) {
        $.ajax({
            url: `/ba-sesudah-pelayaran/${baId}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const ba = response.data;
                    $('#modalTitle').text('Form Edit BA Sesudah Pelayaran');
                    $('#baId').val(ba.trans_id);
                    $('#nomor_surat').val(ba.nomor_surat);

                    // Format tanggal untuk input date (YYYY-MM-DD)
                    if (ba.tanggal_surat) {
                        // Handle different date formats
                        let tanggal;
                        if (ba.tanggal_surat.includes('T')) {
                            // ISO format with time
                            tanggal = new Date(ba.tanggal_surat);
                        } else {
                            // Date only format
                            tanggal = new Date(ba.tanggal_surat + 'T00:00:00');
                        }

                        // Check if date is valid
                        if (!isNaN(tanggal.getTime())) {
                            const formattedDate = tanggal.toISOString().split('T')[0];
                            $('#tanggal_surat').val(formattedDate);
                        } else {
                            $('#tanggal_surat').val('');
                        }
                    } else {
                        $('#tanggal_surat').val('');
                    }

                    $('#jam_surat').val(ba.jam_surat);
                    $('#zona_waktu_surat').val(ba.zona_waktu_surat);
                    $('#lokasi_surat').val(ba.lokasi_surat);
                    $('#volume_sisa').val(ba.volume_sisa);
                    $('#jabatan_staf_pangkalan').val(ba.jabatan_staf_pangkalan);
                    $('#nama_staf_pagkalan').val(ba.nama_staf_pagkalan);
                    $('#nip_staf').val(ba.nip_staf);
                    $('#nama_nahkoda').val(ba.nama_nahkoda);
                    $('#nip_nahkoda').val(ba.nip_nahkoda);
                    $('#nama_kkm').val(ba.nama_kkm);
                    $('#nip_kkm').val(ba.nip_kkm);
                    $('#an_staf').prop('checked', ba.an_staf == 1 || ba.an_staf === true);
                    $('#an_nakhoda').prop('checked', ba.an_nakhoda == 1 || ba.an_nakhoda === true);
                    $('#an_kkm').prop('checked', ba.an_kkm == 1 || ba.an_kkm === true);

                    // Update hidden input values to match checkbox states
                    $('input[name="an_staf"]').val($('#an_staf').is(':checked') ? '1' : '0');
                    $('input[name="an_nakhoda"]').val($('#an_nakhoda').is(':checked') ? '1' : '0');
                    $('input[name="an_kkm"]').val($('#an_kkm').is(':checked') ? '1' : '0');

                    // Update visual feedback for checkboxes
                    $('input[type="checkbox"]').each(function() {
                        const checkbox = $(this);
                        const label = checkbox.next('label');
                        if (checkbox.is(':checked')) {
                            label.addClass('text-blue-600 dark:text-blue-400');
                        } else {
                            label.removeClass('text-blue-600 dark:text-blue-400');
                        }
                    });

                    // Set kapal
                    if (ba.kapal) {
                        $('#kapal_id').val(ba.kapal.m_kapal_id);
                        $('#code_kapal').val(ba.kapal.code_kapal);
                    }

                    $('#baModal').removeClass('hidden');

                    // Focus on first input after modal opens
                    setTimeout(() => {
                        $('#kapal_id').focus();
                    }, 100);
                }
            }
            , error: function() {
                showNotification('error', 'Gagal memuat data BA');
            }
        });
    }

    function deleteBa(baId) {
        if (confirm('Apakah Anda yakin ingin menghapus BA ini?\n\nData yang dihapus tidak dapat dikembalikan.')) {
            showNotification('info', 'Sedang menghapus data...');

            $.ajax({
                url: `/ba-sesudah-pelayaran/${baId}`
                , type: 'DELETE'
                , data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        loadBaData();
                        showNotification('success', response.message);
                    } else {
                        showNotification('error', response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else if (xhr.status === 422) {
                        showNotification('error', 'Tidak dapat menghapus data ini');
                    } else {
                        showNotification('error', 'Gagal menghapus BA');
                    }
                }
            });
        }
    }

    function generatePdf(baId) {
        // Show loading
        showNotification('info', 'Sedang membuat PDF...');

        $.ajax({
            url: `/ba-sesudah-pelayaran/${baId}/pdf`
            , type: 'GET'
            , timeout: 30000 // 30 seconds timeout
            , success: function(response) {
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
            }
        });
    }

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

    function showNotification(type, message) {
        // Use Toastr for notifications
        if (typeof toastr !== 'undefined') {
            switch (type) {
                case 'success':
                    toastr.success(message);
                    break;
                case 'error':
                    toastr.error(message);
                    break;
                case 'warning':
                    toastr.warning(message);
                    break;
                case 'info':
                    toastr.info(message);
                    break;
                default:
                    toastr.info(message);
            }
        } else {
            // Fallback to alert if toastr is not available
            alert(message);
        }
    }

    function showErrorMessage(message) {
        showNotification('error', message);
    }

    // Upload Document Functions
    let currentBaId = null;

    function openUploadModal(baId) {
        currentBaId = baId;

        // Check if BA already has a file to determine modal title
        // We'll need to get this info from the table data or make an AJAX call
        // For now, let's check if the view button is visible (which means file exists)
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
    }

    function viewDocument(baId) {
        currentBaId = baId;
        showNotification('info', 'Memuat dokumen...');

        $.ajax({
            url: `/ba-sesudah-pelayaran/${baId}/view-document`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    $('#documentViewer').html(`
                        <div class="text-center">
                            <div class="mb-4">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">${response.filename}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Dokumen pendukung BA Sesudah Pelayaran</p>
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
                    showNotification('success', 'Dokumen berhasil dimuat');
                } else {
                    showNotification('error', response.message);
                }
            }
            , error: function(xhr) {
                if (xhr.status === 404) {
                    showNotification('error', 'Dokumen tidak ditemukan');
                } else {
                    showNotification('error', 'Gagal memuat dokumen');
                }
            }
        });
    }

    // Upload form submission - bind after DOM ready
    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Form submitted, preventing default');

            if (!currentBaId) {
                showNotification('error', 'ID BA tidak valid');
                return;
            }

            const formData = new FormData(this);
            $('#uploadProgress').removeClass('hidden');
            const isReplace = $('#uploadModalTitle').text().includes('Ganti');
            $('#uploadBtn').prop('disabled', true).html(`
                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${isReplace ? 'Mengganti...' : 'Uploading...'}
            `);

            $.ajax({
                url: `/ba-sesudah-pelayaran/${currentBaId}/upload`
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            $('#progressBar').css('width', percentComplete + '%');
                            $('#progressText').text(`Uploading... ${percentComplete}%`);
                        }
                    }, false);
                    return xhr;
                }
                , success: function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        $('#uploadModal').addClass('hidden');
                        loadBaData(); // Reload data to update button states
                    } else {
                        showNotification('error', response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validasi gagal:\n';
                        for (let field in errors) {
                            errorMessage += `- ${errors[field][0]}\n`;
                        }
                        showNotification('error', errorMessage);
                    } else {
                        showNotification('error', 'Gagal upload dokumen');
                    }
                }
                , complete: function() {
                    const isReplace = $('#uploadModalTitle').text().includes('Ganti');
                    const buttonColor = isReplace ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700';
                    const buttonText = isReplace ? 'Ganti Dokumen' : 'Upload';

                    $('#uploadBtn').prop('disabled', false).removeClass('bg-green-600 hover:bg-green-700 bg-orange-600 hover:bg-orange-700').addClass(buttonColor).html(`
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span id="uploadBtnText">${buttonText}</span>
                    `);
                    $('#uploadProgress').addClass('hidden');
                    $('#progressBar').css('width', '0%');
                    $('#progressText').text('Uploading...');
                }
            });
        });

        // Delete document
        $('#deleteDocumentBtn').on('click', function() {
            if (!currentBaId) {
                showNotification('error', 'ID BA tidak valid');
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                showNotification('info', 'Menghapus dokumen...');

                $.ajax({
                    url: `/ba-sesudah-pelayaran/${currentBaId}/delete-document`
                    , type: 'DELETE'
                    , data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                    , success: function(response) {
                        if (response.success) {
                            showNotification('success', response.message);
                            $('#viewDocumentModal').addClass('hidden');
                            loadBaData(); // Reload data to update button states
                        } else {
                            showNotification('error', response.message);
                        }
                    }
                    , error: function(xhr) {
                        if (xhr.status === 404) {
                            showNotification('error', 'Dokumen tidak ditemukan');
                        } else {
                            showNotification('error', 'Gagal menghapus dokumen');
                        }
                    }
                });
            }
        });

        // Modal event handlers
        $('#closeUploadModal, #cancelUploadBtn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Close upload modal clicked');
            $('#uploadModal').addClass('hidden');
            $('#uploadForm')[0].reset();
            $('#uploadProgress').addClass('hidden');
            currentBaId = null;
        });

        $('#closeViewDocumentModal').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Close view document modal clicked');
            $('#viewDocumentModal').addClass('hidden');
            currentBaId = null;
        });
    });

    // Close modals when clicking outside
    $(document).on('click', function(e) {
        if (e.target.id === 'uploadModal') {
            $('#uploadModal').addClass('hidden');
            $('#uploadForm')[0].reset();
            $('#uploadProgress').addClass('hidden');
            currentBaId = null;
        }
        if (e.target.id === 'viewDocumentModal') {
            $('#viewDocumentModal').addClass('hidden');
            currentBaId = null;
        }
    });

    // ESC key to close modals
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#uploadModal').addClass('hidden');
            $('#viewDocumentModal').addClass('hidden');
            $('#uploadForm')[0].reset();
            $('#uploadProgress').addClass('hidden');
            currentBaId = null;
        }
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
                        <h3 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-white">Form Tambah BA Sesudah Pelayaran</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data berikut untuk membuat Berita Acara Sesudah Pelayaran</p>
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
                                <textarea id="lokasi_surat" name="lokasi_surat" rows="3" required placeholder="Masukkan lokasi pembuatan BA..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors resize-none"></textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Contoh: Pelabuhan Tanjung Priok, Jakarta Utara</p>
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
                                    <input type="date" id="tanggal_surat" name="tanggal_surat" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors" placeholder="Pilih tanggal">
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

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-yellow-900 dark:text-yellow-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Informasi BBM
                        </h4>
                        <div>
                            <label for="volume_sisa" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Sisa BBM Sesudah Pelayaran (Liter) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="volume_sisa" name="volume_sisa" step="0.01" min="0" required placeholder="0.00" class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Liter</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Masukkan volume sisa BBM dalam liter dengan 2 desimal</p>
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Pejabat/Staf UPT (Menyaksikan)
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="hidden" name="an_staf" value="0">
                                <input type="checkbox" id="an_staf" class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="an_staf" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Tandai "An." di depan nama</label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="jabatan_staf_pangkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jabatan</label>
                                    <input type="text" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" placeholder="Contoh: Kepala UPT" maxlength="30" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 30 karakter</p>
                                </div>
                                <div>
                                    <label for="nama_staf_pagkalan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                                    <input type="text" id="nama_staf_pagkalan" name="nama_staf_pagkalan" placeholder="Nama lengkap pejabat" maxlength="30" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 30 karakter</p>
                                </div>
                                <div>
                                    <label for="nip_staf" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                    <input type="text" id="nip_staf" name="nip_staf" placeholder="Nomor Induk Pegawai" maxlength="20" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 20 karakter</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Nakhoda Kapal
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="hidden" name="an_nakhoda" value="0">
                                <input type="checkbox" id="an_nakhoda" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="an_nakhoda" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Tandai "An." di depan nama</label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nama_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Nakhoda</label>
                                    <input type="text" id="nama_nahkoda" name="nama_nahkoda" placeholder="Nama lengkap nakhoda" maxlength="30" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 30 karakter</p>
                                </div>
                                <div>
                                    <label for="nip_nahkoda" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP Nakhoda</label>
                                    <input type="text" id="nip_nahkoda" name="nip_nahkoda" placeholder="NIP nakhoda" maxlength="20" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 20 karakter</p>
                                </div>
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
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="hidden" name="an_kkm" value="0">
                                <input type="checkbox" id="an_kkm" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="an_kkm" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Tandai "An." di depan nama</label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nama_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama KKM</label>
                                    <input type="text" id="nama_kkm" name="nama_kkm" placeholder="Nama lengkap KKM" maxlength="30" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 30 karakter</p>
                                </div>
                                <div>
                                    <label for="nip_kkm" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">NIP KKM</label>
                                    <input type="text" id="nip_kkm" name="nip_kkm" placeholder="NIP KKM" maxlength="20" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 20 karakter</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" id="cancelBtn" class="px-6 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium shadow-sm hover:shadow-md">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan BA
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewBaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail BA Sesudah Pelayaran</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap Berita Acara Sesudah Pelayaran</p>
                    </div>
                    <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="viewBaContent" class="mt-6">
                    <!-- Content will be loaded via AJAX -->
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
<div id="helpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 mt-10 mb-10 max-h-[90vh] overflow-y-auto help-modal-scroll">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan BA Sesudah Pelayaran</h3>
                <button id="closeHelpModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="space-y-6">
                <!-- Overview -->
                <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">📋 Overview</h4>
                    <p class="text-blue-800 dark:text-blue-200">
                        BA Sesudah Pelayaran adalah dokumen yang dibuat setelah kapal selesai melakukan pelayaran.
                        Dokumen ini mencatat kondisi kapal, hasil pelayaran, dan evaluasi setelah berlayar.
                    </p>
                </div>

                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔧 Fitur Utama</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔍 Pencarian & Filter</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Cari BA berdasarkan nomor surat, kapal, atau tanggal. Gunakan filter untuk menemukan data dengan cepat.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">➕ Tambah BA</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol "Tambah BA" untuk membuat BA Sesudah Pelayaran baru.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">✏️ Edit BA</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol edit untuk mengubah data BA yang sudah ada.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">👁️ Lihat Detail</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol detail untuk melihat informasi lengkap BA.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📄 Generate PDF</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol PDF untuk mengunduh dokumen BA dalam format PDF.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📎 Upload Dokumen</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Upload dokumen pendukung untuk BA (foto, scan, dll).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📝 Field Form BA Sesudah Pelayaran</h4>
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📋 Informasi Surat</h5>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• <strong>Nomor Surat:</strong> Nomor BA yang unik</li>
                                <li>• <strong>Tanggal Surat:</strong> Tanggal pembuatan BA</li>
                                <li>• <strong>Jam Surat:</strong> Waktu pembuatan BA</li>
                                <li>• <strong>Lokasi Surat:</strong> Tempat pembuatan BA</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🚢 Informasi Kapal</h5>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• <strong>Kapal:</strong> Pilih kapal yang telah berlayar</li>
                                <li>• <strong>UPT:</strong> Unit Pelaksana Teknis yang bertanggung jawab</li>
                                <li>• <strong>Alamat UPT:</strong> Alamat lengkap UPT</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">⚓ Informasi Pelayaran</h5>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• <strong>Hasil Pelayaran:</strong> Hasil dan pencapaian pelayaran</li>
                                <li>• <strong>Durasi Aktual:</strong> Lama pelayaran yang sebenarnya</li>
                                <li>• <strong>Kondisi Kapal:</strong> Kondisi kapal setelah berlayar</li>
                                <li>• <strong>Evaluasi:</strong> Evaluasi dan catatan pelayaran</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">👤 Informasi Personel</h5>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• <strong>Staf Pangkalan:</strong> Nama dan NIP staf yang bertugas</li>
                                <li>• <strong>Nahkoda:</strong> Nama dan NIP nahkoda kapal</li>
                                <li>• <strong>KKM:</strong> Nama dan NIP Kepala Kamar Mesin</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">💡 Tips Penggunaan</h4>
                    <ul class="text-yellow-800 dark:text-yellow-200 space-y-2">
                        <li>• Pastikan data kapal sudah terdaftar sebelum membuat BA</li>
                        <li>• Isi semua field yang wajib (bertanda *) untuk kelengkapan dokumen</li>
                        <li>• Catat kondisi kapal dan hasil pelayaran dengan detail</li>
                        <li>• Upload dokumen pendukung untuk validasi</li>
                        <li>• Generate PDF untuk arsip dan distribusi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
