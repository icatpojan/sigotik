@extends('layouts.dashboard')

@section('title', 'Manajemen UPT')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen UPT</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola data Unit Pelaksana Teknis</p>
        </div>
        <button id="createUptBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah UPT
        </button>
    </div>

    <!-- Filter and UPTs Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari UPT</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari nama, kode, kota..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('search') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Zona Waktu Filter -->
                <div class="w-full sm:w-40">
                    <label for="zona_waktu" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Zona Waktu</label>
                    <select id="zona_waktu" name="zona_waktu" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Zona</option>
                        <option value="WIB">WIB</option>
                        <option value="WITA">WITA</option>
                        <option value="WIT">WIT</option>
                    </select>
                </div>

                <!-- Per Page Selector -->
                <div class="w-full sm:w-32">
                    <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Per Halaman</label>
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

        <!-- UPTs Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nama & Kode UPT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Alamat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kota & Zona Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Petugas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="uptsTableBody" class="bg-white dark:bg-gray-800">
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
        loadUpts();

        // Modal controls
        $('#createUptBtn, #createFirstUptBtn').click(function() {
            $('#modalTitle').text('Form Tambah UPT');
            $('#uptForm')[0].reset();
            $('#uptId').val('');
            $('#uptModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        $('#closeModal').click(function() {
            $('#uptModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        $('#closeViewModal').click(function() {
            $('#viewUptModal').addClass('hidden');
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1; // Reset to first page when filtering
            loadUpts();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#zona_waktu').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadUpts();
        });

        // UPT form submission
        $('#uptForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const uptId = $('#uptId').val();
            const url = uptId ? `/upts/${uptId}` : '/upts';
            const method = uptId ? 'PUT' : 'POST';

            // Add _method for PUT request
            if (uptId) {
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
                        $('#uptModal').addClass('hidden');
                        loadUpts(); // Reload data instead of page reload
                    } else {
                        alert(response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        // Session expired, redirect to login
                        window.location.href = '/login';
                    } else if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validasi gagal:\n';
                        for (let field in errors) {
                            errorMessage += `- ${errors[field][0]}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        alert('Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });
    });

    // Function to load UPTs data via AJAX
    function loadUpts(page = 1) {
        currentPage = page;

        // Show loading indicator
        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#uptsTableBody').html('');
        $('#paginationContainer').html('');

        // Get current filter values
        const filters = {
            search: $('#search').val()
            , zona_waktu: $('#zona_waktu').val()
            , per_page: $('#perPage').val()
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/upts/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderUptsTable(response.upts);
                    renderPagination(response.pagination, response.links);
                } else {
                    showErrorMessage('Gagal memuat data UPT');
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

    // Function to render UPTs table
    function renderUptsTable(upts) {
        const tbody = $('#uptsTableBody');
        tbody.html('');

        if (upts.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data UPT</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan UPT pertama</p>
                            <button id="createFirstUptBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah UPT Pertama
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        upts.forEach(function(upt) {
            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${upt.nama}</div>
                            <div class="text-gray-500 dark:text-gray-400">Kode: ${upt.code}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white max-w-xs">
                            <div class="break-words">${upt.alamat1 || '-'}</div>
                            <div class="break-words text-gray-500 dark:text-gray-400">${upt.alamat2 || ''}</div>
                            <div class="break-words text-gray-500 dark:text-gray-400">${upt.alamat3 || ''}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Kota: ${upt.kota || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">Zona: ${upt.zona_waktu_upt}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>${upt.nama_petugas || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">${upt.jabatan_petugas || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewUpt(${upt.m_upt_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="editUpt(${upt.m_upt_id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit UPT">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteUpt(${upt.m_upt_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus UPT">
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

    // Function to render pagination
    function renderPagination(pagination, links) {
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
            paginationHtml += `<button onclick="loadUpts(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Sebelumnya
            </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadUpts(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'} border rounded-md">
                ${i}
            </button>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadUpts(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Selanjutnya
            </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    // Function to show error message
    function showErrorMessage(message) {
        $('#uptsTableBody').html(`
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-red-500 border border-gray-300 dark:border-gray-600">
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

    // Global functions
    function editUpt(id) {
        $.ajax({
            url: `/upts/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const upt = response.upt;
                    $('#modalTitle').text('Form Edit UPT');
                    $('#uptId').val(upt.m_upt_id);
                    $('#nama').val(upt.nama);
                    $('#code').val(upt.code);
                    $('#alamat1').val(upt.alamat1);
                    $('#alamat2').val(upt.alamat2);
                    $('#alamat3').val(upt.alamat3);
                    $('#kota').val(upt.kota);
                    $('#zona_waktu_upt').val(upt.zona_waktu_upt);
                    $('#nama_petugas').val(upt.nama_petugas);
                    $('#nip_petugas').val(upt.nip_petugas);
                    $('#jabatan_petugas').val(upt.jabatan_petugas);
                    $('#pangkat_petugas').val(upt.pangkat_petugas);
                    $('#uptModal').removeClass('hidden').addClass('flex items-center justify-center');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    alert('Terjadi kesalahan saat mengambil data UPT');
                }
            }
        });
    }

    function viewUpt(id) {
        $.ajax({
            url: `/upts/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const upt = response.upt;
                    const uptDetails = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-xl font-medium text-white">${upt.nama ? upt.nama.charAt(0) : 'U'}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">${upt.nama}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kode: ${upt.code}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Alamat 1:</span>
                                <p class="text-gray-900 dark:text-white">${upt.alamat1 || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Alamat 2:</span>
                                <p class="text-gray-900 dark:text-white">${upt.alamat2 || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Alamat 3:</span>
                                <p class="text-gray-900 dark:text-white">${upt.alamat3 || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Kota:</span>
                                <p class="text-gray-900 dark:text-white">${upt.kota || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Zona Waktu:</span>
                                <p class="text-gray-900 dark:text-white">${upt.zona_waktu_upt}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Nama Petugas:</span>
                                <p class="text-gray-900 dark:text-white">${upt.nama_petugas || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">NIP Petugas:</span>
                                <p class="text-gray-900 dark:text-white">${upt.nip_petugas || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Jabatan:</span>
                                <p class="text-gray-900 dark:text-white">${upt.jabatan_petugas || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Pangkat:</span>
                                <p class="text-gray-900 dark:text-white">${upt.pangkat_petugas || '-'}</p>
                            </div>
                        </div>
                    </div>
                `;
                    $('#uptDetails').html(uptDetails);
                    $('#viewUptModal').removeClass('hidden');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    alert('Terjadi kesalahan saat mengambil data UPT');
                }
            }
        });
    }

    function deleteUpt(id) {
        if (confirm('Apakah Anda yakin ingin menghapus UPT ini?')) {
            $.ajax({
                url: `/upts/${id}`
                , type: 'POST'
                , data: {
                    _method: 'DELETE'
                    , _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        loadUpts(currentPage); // Reload current page instead of page reload
                    } else {
                        alert(response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        // Session expired, redirect to login
                        window.location.href = '/login';
                    } else {
                        alert('Terjadi kesalahan saat menghapus data');
                    }
                }
            });
        }
    }

</script>
@endsection

@section('modals')
<!-- UPT Modal -->
<div id="uptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-white">Form Tambah UPT</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="uptForm" class="mt-6">
                @csrf
                <input type="hidden" id="uptId" name="upt_id">

                <div class="space-y-4">
                    <!-- Nama UPT -->
                    <div>
                        <label for="nama" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama UPT</label>
                        <input type="text" id="nama" name="nama" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Kode -->
                    <div>
                        <label for="code" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kode</label>
                        <input type="text" id="code" name="code" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Alamat 1 -->
                    <div>
                        <label for="alamat1" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Alamat 1</label>
                        <input type="text" id="alamat1" name="alamat1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Alamat 2 -->
                    <div>
                        <label for="alamat2" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Alamat 2</label>
                        <input type="text" id="alamat2" name="alamat2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Alamat 3 -->
                    <div>
                        <label for="alamat3" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Alamat 3</label>
                        <input type="text" id="alamat3" name="alamat3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Kota dan Zona Waktu -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kota" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kota</label>
                            <input type="text" id="kota" name="kota" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="zona_waktu_upt" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Zona Waktu Wilayah UPT</label>
                            <select id="zona_waktu_upt" name="zona_waktu_upt" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="WIB">WIB</option>
                                <option value="WITA">WITA</option>
                                <option value="WIT">WIT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nama Petugas dan NIP Petugas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_petugas" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Petugas</label>
                            <input type="text" id="nama_petugas" name="nama_petugas" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="nip_petugas" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nip Petugas</label>
                            <input type="text" id="nip_petugas" name="nip_petugas" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Jabatan Petugas dan Pangkat Petugas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="jabatan_petugas" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jabatan Petugas</label>
                            <input type="text" id="jabatan_petugas" name="jabatan_petugas" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="pangkat_petugas" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pangkat Petugas</label>
                            <input type="text" id="pangkat_petugas" name="pangkat_petugas" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" id="submitBtn" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View UPT Modal -->
<div id="viewUptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail UPT</h3>
                <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="uptDetails" class="mt-6">
                <!-- UPT details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection
