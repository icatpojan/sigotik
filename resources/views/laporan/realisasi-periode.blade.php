@extends('layouts.dashboard')

@section('title', 'Laporan Realisasi per Periode')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Realisasi per Periode</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Realisasi Tagihan BBM Per UPT</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filter Laporan</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Periode</label>
                    <select id="periode" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Periode</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">UPT</label>
                    <select id="upt_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih UPT</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="filterBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <button id="resetBtn" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <div class="flex flex-wrap gap-3">
                <button id="previewBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Preview
                </button>
                <button id="printBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
                <button id="excelBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                    <thead style="background-color: #568fd2;">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Periode</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">UPT</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">No Tagihan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tanggal Surat</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Total Realisasi (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody" class="bg-white dark:bg-gray-800">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p>Klik "Preview" untuk melihat data laporan</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toastr configuration
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

        // Load initial data
        loadPeriodeOptions();
        loadUptOptions();

        // Filter button
        $('#filterBtn').click(function() {
            loadData();
        });

        // Reset button
        $('#resetBtn').click(function() {
            $('#periode').val('');
            $('#upt_code').val('');
            $('#dataTableBody').html(`
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>Klik "Preview" untuk melihat data laporan</p>
                    </div>
                </td>
            </tr>
        `);
        });

        // Preview button
        $('#previewBtn').click(function() {
            loadData();
        });

        // Print button
        $('#printBtn').click(function() {
            const periode = $('#periode').val();
            const uptCode = $('#upt_code').val();

            if (!periode) {
                toastr.error('Pilih periode terlebih dahulu');
                return;
            }

            // Create form and submit via POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("laporan-anggaran.export.pdf", "realisasi-periode") }}';
            form.target = '_blank';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            const periodeInput = document.createElement('input');
            periodeInput.type = 'hidden';
            periodeInput.name = 'periode';
            periodeInput.value = periode;
            form.appendChild(periodeInput);

            const uptCodeInput = document.createElement('input');
            uptCodeInput.type = 'hidden';
            uptCodeInput.name = 'upt_code';
            uptCodeInput.value = uptCode;
            form.appendChild(uptCodeInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });

        // Excel button
        $('#excelBtn').click(function() {
            const periode = $('#periode').val();
            const uptCode = $('#upt_code').val();

            if (!periode) {
                toastr.error('Pilih periode terlebih dahulu');
                return;
            }

            // Create form and submit via POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("laporan-anggaran.export.excel", "realisasi-periode") }}';
            form.target = '_blank';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            const periodeInput = document.createElement('input');
            periodeInput.type = 'hidden';
            periodeInput.name = 'periode';
            periodeInput.value = periode;
            form.appendChild(periodeInput);

            const uptCodeInput = document.createElement('input');
            uptCodeInput.type = 'hidden';
            uptCodeInput.name = 'upt_code';
            uptCodeInput.value = uptCode;
            form.appendChild(uptCodeInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });


        function loadPeriodeOptions() {
            $.get('{{ route("laporan-anggaran.anggaran.periodes") }}', function(data) {
                let options = '<option value="">Pilih Periode</option>';
                data.data.forEach(function(item) {
                    options += `<option value="${item.periode}">${item.periode}</option>`;
                });
                $('#periode').html(options);
            }).fail(function() {
                toastr.error('Gagal memuat data periode');
            });
        }

        function loadUptOptions() {
            $.get('{{ route("laporan-anggaran.realisasi-periode.upts") }}', function(data) {
                let options = '<option value="">Pilih UPT</option>';
                data.data.forEach(function(item) {
                    options += `<option value="${item.code}">${item.nama}</option>`;
                });
                $('#upt_code').html(options);
            }).fail(function() {
                toastr.error('Gagal memuat data UPT');
            });
        }

        function loadData() {
            const periode = $('#periode').val();
            const uptCode = $('#upt_code').val();

            if (!periode) {
                toastr.error('Pilih periode terlebih dahulu');
                return;
            }

            // Show loading
            $('#dataTableBody').html(`
            <tr>
                <td colspan="6" class="px-4 py-8 text-center">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span>
                    </div>
                </td>
            </tr>
        `);

            $.get('{{ route("laporan-anggaran.realisasi-periode.data") }}', {
                periode: periode
                , upt_code: uptCode
            }, function(data) {
                if (data.data && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(function(item, index) {
                        html += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                            <td class="px-4 py-3 text-center border border-gray-300 dark:border-gray-600">${item.periode}</td>
                            <td class="px-4 py-3 border border-gray-300 dark:border-gray-600">${item.upt ? item.upt.nama : '-'}</td>
                            <td class="px-4 py-3 text-center border border-gray-300 dark:border-gray-600">${item.no_tagihan}</td>
                            <td class="px-4 py-3 text-center border border-gray-300 dark:border-gray-600">${new Date(item.tanggal_surat).toLocaleDateString('id-ID')}</td>
                            <td class="px-4 py-3 text-right border border-gray-300 dark:border-gray-600 font-medium">Rp. ${new Intl.NumberFormat('id-ID').format(item.total_realisasi)}</td>
                        </tr>
                    `;
                    });
                    $('#dataTableBody').html(html);
                    toastr.success('Data berhasil dimuat');
                } else {
                    $('#dataTableBody').html(`
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>Tidak ada data untuk periode yang dipilih</p>
                            </div>
                        </td>
                    </tr>
                `);
                }
            }).fail(function() {
                $('#dataTableBody').html(`
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-red-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Gagal memuat data</p>
                        </div>
                    </td>
                </tr>
            `);
                toastr.error('Gagal memuat data');
            });
        }
    });

</script>
@endsection
