@extends('layouts.dashboard')

@section('title', 'Manajemen Group')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Group</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola data group/role sistem</p>
        </div>
        <div class="flex gap-2">
            <button id="helpBtn" class="inline-flex items-center px-4 py-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </button>
            <button id="createGroupBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Group
            </button>
        </div>
    </div>

    <!-- Filter and Groups Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Group</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari group..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('search') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
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

        <!-- Groups Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">ID</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nama Group</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Jumlah User</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="groupsTableBody" class="bg-white dark:bg-gray-800">
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
        loadGroups();

        // Help button
        $('#helpBtn').click(function() {
            $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        // Modal controls
        $('#createGroupBtn, #createFirstGroupBtn').click(function() {
            $('#modalTitle').text('Form Tambah Group');
            $('#groupForm')[0].reset();
            $('#groupId').val('');
            $('#groupModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        $('#closeModal').click(function() {
            $('#groupModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        $('#closeViewModal').click(function() {
            $('#viewGroupModal').addClass('hidden');
        });

        // Help modal controls
        $('#closeHelpModal, #closeHelpModalBtn').click(function() {
            $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1; // Reset to first page when filtering
            loadGroups();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadGroups();
        });

        // Group form submission
        $('#groupForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const groupId = $('#groupId').val();
            const url = groupId ? `/groups/${groupId}` : '/groups';
            const method = groupId ? 'PUT' : 'POST';

            // Add _method for PUT request
            if (groupId) {
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
                        $('#groupModal').addClass('hidden');
                        loadGroups(); // Reload data instead of page reload
                        toastr.success(response.message || 'Group berhasil disimpan');
                    } else {
                        toastr.error(response.message || 'Gagal menyimpan group');
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        // Session expired, redirect to login
                        window.location.href = '/login';
                    } else if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validasi gagal: ';
                        for (let field in errors) {
                            errorMessage += `${errors[field][0]} `;
                        }
                        toastr.error(errorMessage);
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });
    });

    // Function to load groups data via AJAX
    function loadGroups(page = 1) {
        currentPage = page;

        // Show loading indicator
        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#groupsTableBody').html('');
        $('#paginationContainer').html('');

        // Get current filter values
        const filters = {
            search: $('#search').val()
            , per_page: $('#perPage').val()
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/groups/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderGroupsTable(response.groups);
                    renderPagination(response.pagination, response.links);
                } else {
                    showErrorMessage('Gagal memuat data group');
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

    // Function to render groups table
    function renderGroupsTable(groups) {
        const tbody = $('#groupsTableBody');
        tbody.html('');

        if (groups.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data group</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan group pertama</p>
                            <button id="createFirstGroupBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Group Pertama
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        groups.forEach(function(group) {
            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            ${group.conf_group_id}
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${group.group}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                ${group.users_count || 0} User
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewGroup(${group.conf_group_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="editGroup(${group.conf_group_id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit Group">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="managePermissions(${group.conf_group_id})" class="p-2 text-purple-600 bg-purple-50 hover:bg-purple-100 border border-purple-200 hover:border-purple-300 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50 dark:border-purple-700 dark:hover:border-purple-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Kelola Permission">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteGroup(${group.conf_group_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus Group">
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
            paginationHtml += `<button onclick="loadGroups(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Sebelumnya
            </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadGroups(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'} border rounded-md">
                ${i}
            </button>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadGroups(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Selanjutnya
            </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    // Function to show error message
    function showErrorMessage(message) {
        $('#groupsTableBody').html(`
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-red-500 border border-gray-300 dark:border-gray-600">
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
    function editGroup(id) {
        $.ajax({
            url: `/groups/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const group = response.group;
                    $('#modalTitle').text('Form Edit Group');
                    $('#groupId').val(group.conf_group_id);
                    $('#group').val(group.group);
                    $('#groupModal').removeClass('hidden').addClass('flex items-center justify-center');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    toastr.error('Terjadi kesalahan saat mengambil data group');
                }
            }
        });
    }

    function viewGroup(id) {
        $.ajax({
            url: `/groups/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const group = response.group;
                    const groupDetails = `
                    <div class="space-y-4">
                        <div class="mb-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">${group.group}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">ID: ${group.conf_group_id}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 text-sm">
                            <div>
                                <span class="font-bold text-gray-700 dark:text-gray-300">Nama Group:</span>
                                <p class="text-gray-900 dark:text-white">${group.group}</p>
                            </div>
                            <div>
                                <span class="font-bold text-gray-700 dark:text-gray-300">ID Group:</span>
                                <p class="text-gray-900 dark:text-white">${group.conf_group_id}</p>
                            </div>
                        </div>
                    </div>
                `;
                    $('#groupDetails').html(groupDetails);
                    $('#viewGroupModal').removeClass('hidden');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    toastr.error('Terjadi kesalahan saat mengambil data group');
                }
            }
        });
    }

    function deleteGroup(id) {
        if (confirm('Apakah Anda yakin ingin menghapus group ini?')) {
            // Create FormData with CSRF token
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: `/groups/${id}`
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , success: function(response) {
                    if (response.success) {
                        loadGroups(currentPage); // Reload current page instead of page reload
                        toastr.success(response.message || 'Group berhasil dihapus');
                    } else {
                        toastr.error(response.message || 'Gagal menghapus group');
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        // Session expired, redirect to login
                        window.location.href = '/login';
                    } else {
                        toastr.error('Terjadi kesalahan saat menghapus data');
                    }
                }
            });
        }
    }

    function managePermissions(groupId) {
        // Load menus and current permissions
        $.when(
            $.ajax({
                url: '/groups/menus/all'
                , type: 'GET'
            })
            , $.ajax({
                url: `/groups/${groupId}/permissions`
                , type: 'GET'
            })
        ).done(function(menusResponse, permissionsResponse) {
            if (menusResponse[0].success && permissionsResponse[0].success) {
                const menus = menusResponse[0].menus;
                const permissions = permissionsResponse[0].permissions;

                // Render permission modal
                renderPermissionModal(groupId, menus, permissions);
                $('#permissionModal').removeClass('hidden').addClass('flex items-center justify-center');
            }
        }).fail(function() {
            toastr.error('Terjadi kesalahan saat memuat data permission');
        });
    }

    function renderPermissionModal(groupId, menus, permissions) {
        const modalContent = `
            <div class="relative mx-auto p-5 border w-11/12 md:w-5/6 lg:w-4/5 xl:w-3/4 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-5 max-h-[95vh] overflow-y-auto">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">Kelola Permission Group</h3>
                        <button id="closePermissionModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="permissionForm">
                        @csrf
                        <div class="mt-6">
                            <div class="mb-6">
                                <label class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                                    <input type="checkbox" id="checkAllPermissions" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Check All</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                                ${menus.map(menu => `
                                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="${menu.id}"
                                               class="permission-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                               ${permissions.includes(menu.id) ? 'checked' : ''}>
                                        <span class="ml-3 text-sm text-gray-900 dark:text-white break-words">${menu.menu}</span>
                                    </label>
                                `).join('')}
                            </div>
                        </div>
                    </form>

                    <!-- Modal Footer -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end space-x-4">
                            <button type="button" id="cancelPermission" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="button" id="savePermissions" class="px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                Simpan Permission
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#permissionModalContent').html(modalContent);
        $('#currentGroupId').val(groupId);

        // Event handlers
        $('#closePermissionModal, #cancelPermission').click(function() {
            $('#permissionModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        // Check all functionality
        $('#checkAllPermissions').change(function() {
            $('.permission-checkbox').prop('checked', this.checked);
        });

        // Save permissions
        $('#savePermissions').click(function() {
            // Use FormData from the form to automatically include CSRF token
            const formData = new FormData(document.getElementById('permissionForm'));

            $.ajax({
                url: `/groups/${groupId}/permissions`
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , success: function(response) {
                    if (response.success) {
                        $('#permissionModal').addClass('hidden');
                        toastr.success('Permission berhasil disimpan');
                    } else {
                        toastr.error(response.message || 'Gagal menyimpan permission');
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validasi gagal: ';
                        for (let field in errors) {
                            errorMessage += `${errors[field][0]} `;
                        }
                        toastr.error(errorMessage);
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan permission');
                    }
                }
            });
        });
    }

</script>
@endsection

@section('modals')
<!-- Group Modal -->
<div id="groupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-white">Form Tambah Group</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="groupForm" class="mt-6">
                @csrf
                <input type="hidden" id="groupId" name="group_id">

                <div class="space-y-4">
                    <div>
                        <label for="group" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Group</label>
                        <input type="text" id="group" name="group" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Masukkan nama group">
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

<!-- View Group Modal -->
<div id="viewGroupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Group</h3>
                <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="groupDetails" class="mt-6">
                <!-- Group details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div id="permissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div id="permissionModalContent">
        <!-- Permission modal content will be loaded here -->
    </div>
</div>

<!-- Hidden input for current group ID -->
<input type="hidden" id="currentGroupId" value="">

<!-- Help Modal -->
<div id="helpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-lg bg-white dark:bg-gray-800 mt-10 mb-10 max-h-[90vh] overflow-y-auto help-modal-scroll">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan Manajemen Group</h3>
                <button id="closeHelpModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-6 space-y-6">
                <!-- Overview -->
                <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">📋 Overview</h4>
                    <p class="text-blue-800 dark:text-blue-200">
                        Halaman ini digunakan untuk mengelola group/role dalam sistem SIGOTIK. Group menentukan hak akses dan permission yang dimiliki oleh user.
                    </p>
                </div>

                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔧 Fitur Utama</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">➕ Tambah Group</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol "Tambah Group" untuk membuat group/role baru.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔍 Pencarian</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Gunakan kolom pencarian untuk menemukan group tertentu.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">👁️ Lihat Detail</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon mata untuk melihat detail group.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">✏️ Edit Group</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon pensil untuk mengedit nama group.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔐 Kelola Permission</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon kunci untuk mengatur permission group.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🗑️ Hapus Group</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon trash untuk menghapus group.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Permission Management -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔐 Manajemen Permission</h4>
                    <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg">
                        <h5 class="font-medium text-purple-900 dark:text-purple-100 mb-2">Cara Mengatur Permission:</h5>
                        <ol class="text-purple-800 dark:text-purple-200 space-y-1 text-sm">
                            <li>1. Klik ikon kunci pada group yang ingin diatur</li>
                            <li>2. Centang menu yang boleh diakses oleh group tersebut</li>
                            <li>3. Gunakan "Check All" untuk memilih semua menu</li>
                            <li>4. Klik "Simpan Permission" untuk menyimpan perubahan</li>
                        </ol>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">💡 Tips</h4>
                    <ul class="text-yellow-800 dark:text-yellow-200 space-y-1">
                        <li>• Buat group dengan nama yang jelas dan mudah dipahami</li>
                        <li>• Atur permission sesuai dengan kebutuhan kerja</li>
                        <li>• Jangan berikan permission yang tidak diperlukan</li>
                        <li>• Review permission secara berkala untuk keamanan</li>
                        <li>• Group yang sudah memiliki user tidak bisa dihapus</li>
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
@endsection
