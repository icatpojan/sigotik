@extends('layouts.dashboard')

@section('title', 'Pembatalan Realisasi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pembatalan Realisasi</h1>
            <p class="text-gray-600 dark:text-gray-400">Batalkan realisasi anggaran yang telah disetujui</p>
        </div>
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
                        <option value="1">Disetujui</option>
                        <option value="0">Belum Disetujui</option>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Total Realisasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Keterangan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">User Input</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tanggal Input</th>
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

        $.get('{{ route("anggaran.pembatalan-realisasi.data") }}', {
            search: search
            , status: status
            , date_from: dateFrom
            , date_to: dateTo
        }, function(response) {
            $('#loadingIndicator').addClass('hidden');

            if (!response.data || response.data.length === 0) {
                $('#dataTableBody').html(`
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data realisasi
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            response.data.forEach(function(item, index) {
                const statusBadge = item.statusanggaran == 1 ?
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Disetujui</span>' :
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Belum Disetujui</span>';

                const tanggalInput = new Date(item.tanggal_input).toLocaleDateString('id-ID');
                const totalRealisasi = 'Rp. ' + new Intl.NumberFormat('id-ID').format(item.total_anggaran || 0);

                let actions = '<div class="flex items-center justify-end space-x-1">';
                actions += '<button onclick="viewAnggaran(\'' + item.periode + '\')" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>';
                if (item.statusanggaran == 1) {
                    actions += '<button onclick="cancelAnggaran(\'' + item.periode + '\')" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Batalkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>';
                }
                actions += '</div>';

                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${item.periode}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${totalRealisasi}</div>
                        </td>
                        <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.keterangan || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${item.user_input || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-900 dark:text-white">${tanggalInput}</div>
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

    function viewAnggaran(periode) {
        console.log('Viewing anggaran for periode:', periode);

        // Show modal immediately
        $('#viewModal').removeClass('hidden');

        // Show loading in modal
        $('#viewModalBody').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span></div>');

        $.get('{{ route("anggaran.pembatalan-realisasi.view", [":periode"]) }}'.replace(':periode', periode), function(data) {
            console.log('View data received:', data);

            let html = '<div class="overflow-x-auto">';
            html += '<table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">';
            html += '<thead style="background-color: #568fd2;">';
            html += '<tr>';
            html += '<th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No</th>';
            html += '<th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">UPT</th>';
            html += '<th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Realisasi (Rp)</th>';
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
        }).fail(function(xhr, status, error) {
            console.error('Error loading view data:', error);
            $('#viewModalBody').html('<div class="text-center py-8 text-red-500">Gagal memuat data detail</div>');
            toastr.error('Gagal memuat data detail');
        });
    }

    function cancelAnggaran(periode) {
        if (confirm('Apakah Anda yakin ingin membatalkan realisasi periode ' + periode + '?')) {
            $.ajax({
                url: '{{ route("anggaran.pembatalan-realisasi.cancel") }}'
                , type: 'POST'
                , data: {
                    periode: periode
                    , _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        toastr.success('Realisasi berhasil dibatalkan');
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

    function closeViewModal() {
        $('#viewModal').addClass('hidden');
    }

</script>
@endsection
