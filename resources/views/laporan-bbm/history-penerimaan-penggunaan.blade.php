@extends('layouts.dashboard')

@section('title', 'History Penerimaan & Penggunaan BBM')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">History Penerimaan & Penggunaan BBM</h1>
            <p class="text-gray-600 dark:text-gray-400">Riwayat penerimaan dan penggunaan BBM</p>
        </div>
    </div>

    <!-- Filter and Report in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Start Date -->
                <div class="w-full sm:w-40">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- End Date -->
                <div class="w-full sm:w-40">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Akhir</label>
                    <input type="date" id="end_date" name="end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- UPT Filter -->
                <div class="w-full sm:w-40">
                    <label for="upt_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UPT</label>
                    <select id="upt_id" name="upt_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua UPT</option>
                    </select>
                </div>

                <!-- Kapal Filter -->
                <div class="w-full sm:w-40">
                    <label for="kapal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kapal</label>
                    <select id="kapal_id" name="kapal_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Kapal</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="w-full sm:w-auto">
                    <button type="button" onclick="loadData()" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 hover:border-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 dark:border-blue-600 dark:hover:border-blue-700 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                </div>

                <!-- Export Buttons -->
                <div class="w-full sm:w-auto">
                    <button type="button" onclick="exportData('excel')" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 border border-green-600 hover:border-green-700 dark:bg-green-600 dark:hover:bg-green-700 dark:border-green-600 dark:hover:border-green-700 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                            Excel
                    </button>
                </div>

                <div class="w-full sm:w-auto">
                    <button type="button" onclick="exportData('pdf')" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 border border-red-600 hover:border-red-700 dark:bg-red-600 dark:hover:bg-red-700 dark:border-red-600 dark:hover:border-red-700 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                            PDF
                    </button>
                </div>


            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tanggal Transaksi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">UPT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kapal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Jenis BBM</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Jumlah</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="dataTable" class="bg-white dark:bg-gray-800">
                    <!-- Data will be loaded here -->
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
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        loadUptOptions();

        // Load kapal options (all kapals initially)
        loadKapalOptions('');

        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        $('#start_date').val(firstDay.toISOString().split('T')[0]);
        $('#end_date').val(lastDay.toISOString().split('T')[0]);

        $('#upt_id').change(function() {
            loadKapalOptions($(this).val());
        });

        // Handle form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            loadData();
        });

        // Handle filter changes
        $('#start_date, #end_date, #upt_id, #kapal_id').on('change', function() {
            loadData();
        });
        // Force date input to work properly
        $('#start_date, #end_date').on('focus', function() {
            if (this.showPicker) {
                this.showPicker();
            }
        });

        // Add click handler for date input
        $('#start_date, #end_date').on('click', function() {
            if (this.showPicker) {
                this.showPicker();
            }
        });

        // Add touch handler for mobile
        $('#start_date, #end_date').on('touchstart', function() {
            if (this.showPicker) {
                this.showPicker();
            }
        });

        // Add keyboard handler
        $('#start_date, #end_date').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (this.showPicker) {
                    this.showPicker();
                }
            }
        });

        // Add double-click handler
        $('#start_date, #end_date').on('dblclick', function() {
            if (this.showPicker) {
                this.showPicker();
            }
        });

        // Add mousedown handler
        $('#start_date, #end_date').on('mousedown', function() {
            if (this.showPicker) {
                this.showPicker();
            }
        });


        loadData();
    });

    function loadUptOptions() {
        $.get('/laporan-bbm/upt-options', function(data) {
            const select = $('#upt_id');
            select.empty().append('<option value="">Semua UPT</option>');
            data.forEach(function(upt) {
                select.append(`<option value="${upt.id}">${upt.nama_upt}</option>`);
            });
        });
    }

    function loadKapalOptions(uptId) {
        $.get('/laporan-bbm/kapal-options', {
            upt_id: uptId
        }, function(data) {
            const select = $('#kapal_id');
            select.empty().append('<option value="">Semua Kapal</option>');
            data.forEach(function(kapal) {
                select.append(`<option value="${kapal.id}">${kapal.nama_kapal}</option>`);
            });
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const day = date.getDate().toString().padStart(2, '0');
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${day} ${month} ${year} ${hours}:${minutes}`;
    }

    function loadData() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const uptId = $('#upt_id').val();
        const kapalId = $('#kapal_id').val();

        // Show loading indicator
        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#dataTable').html('');

        $.ajax({
            url: '{{ route("laporan-bbm.history-penerimaan-penggunaan.data") }}'
            , method: 'GET'
            , data: {
                start_date: startDate
                , end_date: endDate
                , upt_id: uptId
                , kapal_id: kapalId
            }
            , success: function(response) {
                const tbody = $('#dataTable');
                tbody.html('');

                if (response.data.length > 0) {
                    response.data.forEach(function(item, index) {
                        const row = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                            <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                                <div class="text-sm text-gray-900 dark:text-white">${item.tgl_trans}</div>
                            </td>
                            <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                                <div class="text-sm text-gray-900 dark:text-white">${item.upt ? item.upt.nama_upt : '-'}</div>
                            </td>
                            <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                                <div class="text-sm text-gray-900 dark:text-white">${item.kapal ? item.kapal.nama_kapal : '-'}</div>
                            </td>
                            <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                                <div class="text-sm text-gray-900 dark:text-white">${item.jenis_bbm || '-'}</div>
                            </td>
                            <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                                <div class="text-sm text-gray-900 dark:text-white">${item.jumlah || 0} Liter</div>
                            </td>
                            <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.status_trans == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'}">
                                    ${item.status_trans == 1 ? 'Penerimaan' : 'Penggunaan'}
                                </span>
                            </td>
                            <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                                <div class="text-sm text-gray-900 dark:text-white">${item.keterangan || '-'}</div>
                            </td>
                        </tr>
                    `;
                        tbody.append(row);
                    });
                } else {
                    tbody.html(`
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                            <div class="flex flex-col items-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data</h3>
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada data untuk periode yang dipilih</p>
                            </div>
                        </td>
                    </tr>
                `);
                }
            }
            , error: function(xhr) {
                console.error('Error loading data:', xhr);
                $('#dataTable').html(`
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-red-500 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-red-600 mb-2">Error</h3>
                            <p class="text-red-500">Terjadi kesalahan saat memuat data</p>
                        </div>
                    </td>
                </tr>
            `);
            }
            , complete: function() {
                $('#loadingIndicator').addClass('hidden').removeClass('flex');
            }
        });
    }

    function exportData(format = 'excel') {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const uptId = $('#upt_id').val();

        // Show loading indicator
        const exportButton = event.target;
        const originalText = exportButton.innerHTML;
        exportButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Exporting...';
        exportButton.disabled = true;

        const params = new URLSearchParams({
            start_date: startDate
            , end_date: endDate
            , upt_id: uptId
            , format: format
        });

        // Use fetch to handle the response
        fetch(`{{ route("laporan-bbm.history-penerimaan-penggunaan.export") }}?${params}`)
            .then(response => {
                if (response.ok) {
                    // If successful, download the file
                    return response.blob();
                } else {
                    // If error, get the error message
                    return response.json().then(data => {
                        throw new Error(data.message || 'Export failed');
                    });
                }
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `laporan_${format}_${new Date().toISOString().split('T')[0]}.${format === 'excel' ? 'xlsx' : 'pdf'}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            })
            .catch(error => {
                // Show error message
                alert('Error: ' + error.message);
            })
            .finally(() => {
                // Reset button
                exportButton.innerHTML = originalText;
                exportButton.disabled = false;
            });
    }

</script>
@endsection
