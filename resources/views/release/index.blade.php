@extends('layouts.dashboard')

@section('title', 'Release BBM Kapal Trans')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Release BBM Kapal Trans</h1>
            <p class="text-gray-600 dark:text-gray-400">Daftar transaksi BBM kapal</p>
        </div>
        <div class="flex gap-2">
            <button id="helpBtn" class="inline-flex items-center px-4 py-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </button>
        </div>
    </div>

    <!-- Filter and Data Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Data</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari nomor surat, kapal, nahkoda..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('search') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kapal Filter -->
                <div>
                    <label for="kapal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Kapal</label>
                    <select id="kapal" name="kapal" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Kapal</option>
                        @foreach($kapals as $kapal)
                        <option value="{{ $kapal->code_kapal }}" {{ request('kapal') == $kapal->code_kapal ? 'selected' : '' }}>{{ $kapal->nama_kapal }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Transaksi</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Input</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approval</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                <!-- Jenis Transport Filter -->
                <div>
                    <label for="jenis_transport" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Transport</label>
                    <select id="jenis_transport" name="jenis_transport" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Jenis</option>
                        <option value="0" {{ request('jenis_transport') == '0' ? 'selected' : '' }}>Kosong</option>
                        <option value="1" {{ request('jenis_transport') == '1' ? 'selected' : '' }}>Mobil</option>
                        <option value="2" {{ request('jenis_transport') == '2' ? 'selected' : '' }}>Kapal</option>
                        <option value="3" {{ request('jenis_transport') == '3' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Dari</label>
                    <input type="date" id="date_from" name="date_from" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('date_from') }}">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Sampai</label>
                    <input type="date" id="date_to" name="date_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('date_to') }}">
                </div>

                <!-- Per Page Selector -->
                <div>
                    <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Per Halaman</label>
                    <select id="perPage" name="per_page" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Clear Filter Button -->
                <div class="flex items-end">
                    <button type="button" id="clearFilter" class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No. Surat & Kapal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tanggal & Lokasi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Volume BBM</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nahkoda & KKM</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bbmKapaltransTableBody" class="bg-white dark:bg-gray-800">
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

<!-- Help Modal -->
<div id="helpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 mt-10 mb-10 max-h-[90vh] overflow-y-auto help-modal-scroll">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan Release BBM Kapal Trans</h3>
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
                        Halaman ini menampilkan daftar transaksi BBM kapal yang dapat di-release.
                        Release data akan menghapus file upload yang terkait dan mereset status upload.
                    </p>
                </div>

                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔧 Fitur Utama</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔍 Pencarian & Filter</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Cari berdasarkan nomor surat, lokasi, nama nahkoda, KKM, AN, nomor nota, atau nama kapal.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📊 Filter Status</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Filter berdasarkan status transaksi: Input, Approval, atau Batal.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🚢 Filter Kapal</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Filter berdasarkan kapal tertentu untuk melihat transaksi spesifik.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📅 Filter Tanggal</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Filter berdasarkan rentang tanggal transaksi.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">👁️ Detail Transaksi</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol detail untuk melihat informasi lengkap transaksi.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📄 Pagination</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Navigasi halaman untuk melihat data lebih banyak.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔄 Release Data</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Hanya admin yang dapat me-release data untuk menghapus file upload.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filter Options -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔧 Opsi Filter</h4>
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔍 Search Box</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Ketik kata kunci untuk mencari transaksi berdasarkan nomor surat, lokasi, nama nahkoda, KKM, AN, nomor nota, atau nama kapal.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🚢 Filter Kapal</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Pilih kapal tertentu untuk melihat transaksi yang terkait dengan kapal tersebut.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📊 Status Transaksi</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Lihat status transaksi: Input, Approval, atau Batal.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📄 Pagination</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Navigasi halaman untuk melihat data lebih banyak.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔄 Release Data</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Hanya admin yang dapat me-release data untuk menghapus file upload.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">💡 Tips</h4>
                    <ul class="text-yellow-800 dark:text-yellow-200 space-y-1">
                        <li>• Gunakan filter untuk menemukan transaksi tertentu dengan cepat</li>
                        <li>• Klik "Clear Filter" untuk mereset semua filter</li>
                        <li>• Gunakan pagination untuk melihat data lebih banyak</li>
                        <li>• Status warna menunjukkan kondisi transaksi</li>
                        <li>• Klik detail untuk melihat informasi lengkap</li>
                        <li>• Button Release hanya muncul untuk admin (group_id = 1)</li>
                        <li>• Release akan menghapus file upload dan reset status upload</li>
                    </ul>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button id="closeHelpModalBtn" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* Custom scrollbar for help modal */
    .help-modal-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .help-modal-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .help-modal-scroll::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .help-modal-scroll::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Dark mode scrollbar */
    .dark .help-modal-scroll::-webkit-scrollbar-track {
        background: #374151;
    }

    .dark .help-modal-scroll::-webkit-scrollbar-thumb {
        background: #6b7280;
    }

    .dark .help-modal-scroll::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

</style>


<script>
    let currentPage = 1;
    let currentFilters = {};

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
        // Load initial data
        loadBbmKapaltrans();

        // Help button
        $('#helpBtn').click(function() {
            $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        // Help modal controls
        $('#closeHelpModal, #closeHelpModalBtn').click(function() {
            $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1; // Reset to first page when filtering
            loadBbmKapaltrans();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#kapal').val('');
            $('#status').val('');
            $('#jenis_transport').val('');
            $('#date_from').val('');
            $('#date_to').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadBbmKapaltrans();
        });
    });

    // Function to load BBM kapal trans data via AJAX
    function loadBbmKapaltrans(page = 1) {
        currentPage = page;

        // Show loading indicator
        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#bbmKapaltransTableBody').html('');
        $('#paginationContainer').html('');

        // Get current filter values
        const filters = {
            search: $('#search').val()
            , kapal: $('#kapal').val()
            , status: $('#status').val()
            , jenis_transport: $('#jenis_transport').val()
            , date_from: $('#date_from').val()
            , date_to: $('#date_to').val()
            , per_page: $('#perPage').val()
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/release/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderBbmKapaltransTable(response.bbm_kapaltrans);
                    renderPagination(response.pagination);
                } else {
                    showErrorMessage('Gagal memuat data BBM kapal trans');
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

    // Function to render BBM kapal trans table
    function renderBbmKapaltransTable(bbmKapaltrans) {
        const tbody = $('#bbmKapaltransTableBody');
        tbody.html('');

        if (bbmKapaltrans.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data BBM kapal trans</h3>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada transaksi BBM kapal yang tercatat</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        bbmKapaltrans.forEach(function(bbm) {
            // Format tanggal
            const tanggalSurat = bbm.tanggal_surat ? new Date(bbm.tanggal_surat).toLocaleDateString('id-ID') : '-';

            // Format status
            let statusText = '';
            let statusClass = '';
            switch (bbm.status_trans) {
                case 0:
                    statusText = 'Input';
                    statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
                    break;
                case 1:
                    statusText = 'Approval';
                    statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                    break;
                case 2:
                    statusText = 'Batal';
                    statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
                    break;
                default:
                    statusText = 'Unknown';
                    statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
            }

            // Format jenis transport
            let jenisTransportText = '';
            switch (bbm.jenis_tranport) {
                case '0':
                    jenisTransportText = 'Kosong';
                    break;
                case '1':
                    jenisTransportText = 'Mobil';
                    break;
                case '2':
                    jenisTransportText = 'Kapal';
                    break;
                case '3':
                    jenisTransportText = 'Lainnya';
                    break;
                default:
                    jenisTransportText = '-';
            }

            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">No. Surat: ${bbm.nomor_surat || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">Kapal: ${bbm.kapal ? bbm.kapal.nama_kapal : '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">No. Nota: ${bbm.nomor_nota || '-'}</div>
                        </div>
                    </td>
                    <td class="px-4 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Tanggal: ${tanggalSurat}</div>
                            <div class="text-gray-500 dark:text-gray-400">Jam: ${bbm.jam_surat || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">Lokasi: ${bbm.lokasi_surat || '-'}</div>
                        </div>
                    </td>
                    <td class="px-4 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Sisa: ${bbm.volume_sisa || 0} L</div>
                            <div class="text-gray-500 dark:text-gray-400">Sebelum: ${bbm.volume_sebelum || 0} L</div>
                            <div class="text-gray-500 dark:text-gray-400">Pengisian: ${bbm.volume_pengisian || 0} L</div>
                            <div class="text-gray-500 dark:text-gray-400">Pemakaian: ${bbm.volume_pemakaian || 0} L</div>
                        </div>
                    </td>
                    <td class="px-4 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">Nahkoda: ${bbm.nama_nahkoda || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">KKM: ${bbm.nama_kkm || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">AN: ${bbm.nama_an || '-'}</div>
                        </div>
                    </td>
                    <td class="px-4 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                                ${statusText}
                            </span>
                            <div class="mt-1 text-gray-500 dark:text-gray-400 text-xs">
                                Transport: ${jenisTransportText}
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewBbmKapaltrans(${bbm.trans_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            ${bbm.can_release ? `
                            <button onclick="releaseBbmKapaltrans(${bbm.trans_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Release Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            ` : ''}
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
            paginationHtml += `<button onclick="loadBbmKapaltrans(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Sebelumnya
            </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadBbmKapaltrans(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'} border rounded-md">
                ${i}
            </button>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadBbmKapaltrans(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Selanjutnya
            </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    // Function to show error message
    function showErrorMessage(message) {
        $('#bbmKapaltransTableBody').html(`
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-red-500 border border-gray-300 dark:border-gray-600">
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

    // Function to release BBM kapal trans data
    function releaseBbmKapaltrans(transId) {
        if (confirm('Apakah Anda yakin ingin me-release data ini?')) {
            $.ajax({
                url: '/release/release'
                , type: 'POST'
                , data: {
                    trans_id: transId
                    , _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        loadBbmKapaltrans(currentPage); // Reload data
                    } else {
                        toastr.error(response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else if (xhr.status === 403) {
                        toastr.error('Anda tidak memiliki izin untuk melakukan aksi ini');
                    } else {
                        toastr.error('Terjadi kesalahan saat me-release data');
                    }
                }
            });
        }
    }

    // Global functions
    function viewBbmKapaltrans(id) {
        $.ajax({
            url: `/release/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const bbm = response.bbm_kapaltrans;
                    const bbmDetails = `
                    <div class="space-y-6">
                        <!-- Header Info -->
                        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Informasi Transaksi</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-blue-700 dark:text-blue-300">No. Surat:</span>
                                    <p class="text-blue-900 dark:text-blue-100">${bbm.nomor_surat || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-blue-700 dark:text-blue-300">No. Nota:</span>
                                    <p class="text-blue-900 dark:text-blue-100">${bbm.nomor_nota || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-blue-700 dark:text-blue-300">Kapal:</span>
                                    <p class="text-blue-900 dark:text-blue-100">${bbm.kapal ? bbm.kapal.nama_kapal : '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-blue-700 dark:text-blue-300">Status:</span>
                                    <p class="text-blue-900 dark:text-blue-100">${bbm.status_trans == 0 ? 'Input' : bbm.status_trans == 1 ? 'Approval' : 'Batal'}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal & Lokasi -->
                        <div>
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-3">Tanggal & Lokasi</h5>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal Surat:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.tanggal_surat ? new Date(bbm.tanggal_surat).toLocaleDateString('id-ID') : '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Jam Surat:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.jam_surat || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Lokasi:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.lokasi_surat || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Zona Waktu:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.zona_waktu_surat || '-'}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Volume BBM -->
                        <div>
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-3">Volume BBM (Liter)</h5>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Volume Sisa:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.volume_sisa || 0} L</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Volume Sebelum:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.volume_sebelum || 0} L</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Volume Pengisian:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.volume_pengisian || 0} L</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Volume Pemakaian:</span>
                                    <p class="text-gray-900 dark:text-white">${bbm.volume_pemakaian || 0} L</p>
                                </div>
                            </div>
                        </div>

                        <!-- Petugas -->
                        <div>
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-3">Petugas</h5>
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                    <h6 class="font-medium text-gray-900 dark:text-white mb-2">Nahkoda</h6>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div><span class="font-medium">Nama:</span> ${bbm.nama_nahkoda || '-'}</div>
                                        <div><span class="font-medium">NIP:</span> ${bbm.nip_nahkoda || '-'}</div>
                                        <div><span class="font-medium">Jabatan:</span> ${bbm.jabatan_nahkoda || '-'}</div>
                                        <div><span class="font-medium">Pangkat:</span> ${bbm.pangkat_nahkoda || '-'}</div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                    <h6 class="font-medium text-gray-900 dark:text-white mb-2">KKM</h6>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div><span class="font-medium">Nama:</span> ${bbm.nama_kkm || '-'}</div>
                                        <div><span class="font-medium">NIP:</span> ${bbm.nip_kkm || '-'}</div>
                                        <div><span class="font-medium">Jabatan:</span> ${bbm.jabatan_kkm || '-'}</div>
                                        <div><span class="font-medium">Pangkat:</span> ${bbm.pangkat_kkm || '-'}</div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                    <h6 class="font-medium text-gray-900 dark:text-white mb-2">AN (Agen Navigasi)</h6>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div><span class="font-medium">Nama:</span> ${bbm.nama_an || '-'}</div>
                                        <div><span class="font-medium">NIP:</span> ${bbm.nip_an || '-'}</div>
                                        <div><span class="font-medium">Jabatan:</span> ${bbm.jabatan_an || '-'}</div>
                                        <div><span class="font-medium">Pangkat:</span> ${bbm.pangkat_an || '-'}</div>
                                    </div>
                                </div>
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
                    toastr.error('Terjadi kesalahan saat mengambil data BBM kapal trans');
                }
            }
        });
    }

</script>
@endsection

@section('modals')
<!-- View BBM Kapal Trans Modal -->
<div id="viewBbmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-10">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail BBM Kapal Trans</h3>
                <button id="closeViewBbmModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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

<script>
    $('#closeViewBbmModal').click(function() {
        $('#viewBbmModal').addClass('hidden');
    });

</script>
@endsection
