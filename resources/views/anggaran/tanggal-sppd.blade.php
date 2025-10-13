@extends('layouts.dashboard')

@section('title', 'Tanggal SPPD')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tanggal SPPD</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola tanggal SPPD untuk setiap periode</p>
        </div>
        <button onclick="showAddForm()" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Tanggal SPPD
        </button>
    </div>

    <!-- Filter and Data Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cari Periode</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari periode..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tanggal SPPD</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Keterangan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">User Input</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Tanggal Input</th>
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

<!-- Add/Edit Modal -->
<div id="anggaranModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="flex items-center justify-center min-h-full py-8">
        <div class="relative mx-auto p-6 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 max-h-[95vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 id="anggaranModalLabel" class="text-xl font-semibold text-gray-900 dark:text-white">Form Tanggal SPPD</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data berikut untuk mengatur tanggal SPPD</p>
                    </div>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <form id="anggaranForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="periode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" id="periode" name="periode" required>
                                    <option value="">Pilih Periode</option>
                                    @for($i = 2020; $i <= date('Y') + 5; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                </select>
                            </div>
                            <div>
                                <label for="tanggal_sppd" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal SPPD <span class="text-red-500">*</span></label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" id="tanggal_sppd" name="tanggal_sppd" required>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" id="keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 hover:border-gray-400 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:border-gray-600 dark:hover:border-gray-500 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </button>
                    <button type="button" onclick="saveAnggaran()" class="px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 hover:border-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 dark:border-blue-600 dark:hover:border-blue-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan
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
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        $.get('{{ route("anggaran.tanggal-sppd.data") }}', {
            search: search
            , date_from: dateFrom
            , date_to: dateTo
        }, function(response) {
            $('#loadingIndicator').addClass('hidden');

            if (!response.data || response.data.length === 0) {
                $('#dataTableBody').html(`
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data tanggal SPPD
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            response.data.forEach(function(item, index) {
                const tanggalInput = new Date(item.tanggal_input).toLocaleDateString('id-ID');
                const tanggalSppd = new Date(item.tanggal_sppd).toLocaleDateString('id-ID');

                let actions = '<div class="flex items-center justify-end space-x-1">';
                actions += '<button onclick="editAnggaran(\'' + item.periode + '\')" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>';
                actions += '<button onclick="deleteAnggaran(\'' + item.periode + '\')" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
                actions += '</div>';

                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">${index + 1}</td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${item.periode}</div>
                        </td>
                        <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${tanggalSppd}</div>
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

    function showAddForm() {
        $('#anggaranModalLabel').text('Tambah Tanggal SPPD');
        $('#anggaranForm')[0].reset();
        $('#anggaranModal').removeClass('hidden');
    }

    function closeModal() {
        $('#anggaranModal').addClass('hidden');
    }

    function saveAnggaran() {
        let data = {
            periode: $('#periode').val()
            , tanggal_sppd: $('#tanggal_sppd').val()
            , keterangan: $('#keterangan').val()
        };

        // Determine if this is edit or create
        let isEdit = $('#anggaranModalLabel').text().includes('Edit');
        let url = isEdit ?
            '{{ route("anggaran.tanggal-sppd.update") }}' :
            '{{ route("anggaran.tanggal-sppd.create") }}';

        $.ajax({
            url: url
            , type: 'POST'
            , data: data
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(response) {
                if (response.success) {
                    toastr.success('Data berhasil disimpan');
                    $('#anggaranModal').addClass('hidden');
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

    function editAnggaran(periode) {
        $.get('{{ route("anggaran.tanggal-sppd.edit", [":periode"]) }}'.replace(':periode', periode), function(data) {
            $('#periode').val(data.periode);
            $('#tanggal_sppd').val(data.tanggal_sppd);
            $('#keterangan').val(data.keterangan);

            $('#anggaranModalLabel').text('Edit Tanggal SPPD');
            $('#anggaranModal').removeClass('hidden');
        });
    }

    function deleteAnggaran(periode) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '{{ route("anggaran.tanggal-sppd.delete", [":periode"]) }}'.replace(':periode', periode)
                , type: 'DELETE'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        toastr.success('Data berhasil dihapus');
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

</script>
@endsection
