@extends('layouts.dashboard')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen User</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola data pengguna sistem</p>
        </div>
        <div class="flex gap-2">
            <button id="helpBtn" class="inline-flex items-center px-4 py-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </button>
            <button id="createUserBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah User
            </button>
        </div>
    </div>

    <!-- Filter and Users Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari User/Kapal</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari user atau kapal..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('search') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Role Filter -->
                <div class="w-full sm:w-40">
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Role</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Role</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->conf_group_id }}" {{ request('role') == $group->conf_group_id ? 'selected' : '' }}>{{ $group->group }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- UPT Filter -->
                <div class="w-full sm:w-40">
                    <label for="upt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter UPT</label>
                    <select id="upt" name="upt" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua UPT</option>
                        @foreach($upts as $upt)
                        <option value="{{ $upt->code }}" {{ request('upt') == $upt->code ? 'selected' : '' }}>{{ $upt->nama }}</option>
                        @endforeach
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

        <!-- Users Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Username & Nama Lengkap</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Role & UPT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nip dan Gol</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kapal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" class="bg-white dark:bg-gray-800">
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
        loadUsers();

        // Help button
        $('#helpBtn').click(function() {
            $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        // Modal controls
        $('#createUserBtn, #createFirstUserBtn').click(function() {
            $('#modalTitle').text('Form Tambah User');
            $('#userForm')[0].reset();
            $('#userId').val('');
            $('#password').attr('required', true);
            $('#passwordHelpText').addClass('hidden'); // Hide help text for create mode
            $('#userModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        $('#closeModal').click(function() {
            $('#userModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        $('#closeViewModal').click(function() {
            $('#viewUserModal').addClass('hidden');
        });

        // Help modal controls
        $('#closeHelpModal, #closeHelpModalBtn').click(function() {
            $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1; // Reset to first page when filtering
            loadUsers();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#role').val('');
            $('#upt').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadUsers();
        });

        // User form submission
        $('#userForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const userId = $('#userId').val();
            const url = userId ? `/users/${userId}` : '/users';
            const method = userId ? 'PUT' : 'POST';

            // Add _method for PUT request
            if (userId) {
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
                        $('#userModal').addClass('hidden');
                        loadUsers(); // Reload data instead of page reload
                        toastr.success(response.message || 'User berhasil disimpan');
                    } else {
                        toastr.error(response.message || 'Gagal menyimpan user');
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

    // Function to load users data via AJAX
    function loadUsers(page = 1) {
        currentPage = page;

        // Show loading indicator
        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#usersTableBody').html('');
        $('#paginationContainer').html('');

        // Get current filter values
        const filters = {
            search: $('#search').val()
            , role: $('#role').val()
            , upt: $('#upt').val()
            , per_page: $('#perPage').val()
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/users/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderUsersTable(response.users);
                    renderPagination(response.pagination, response.links);
                } else {
                    toastr.error('Gagal memuat data user');
                    showErrorMessage('Gagal memuat data user');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    toastr.error('Terjadi kesalahan saat memuat data');
                    showErrorMessage('Terjadi kesalahan saat memuat data');
                }
            }
            , complete: function() {
                $('#loadingIndicator').addClass('hidden').removeClass('flex');
            }
        });
    }

    // Function to render users table
    function renderUsersTable(users) {
        const tbody = $('#usersTableBody');
        tbody.html('');

        if (users.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data user</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan user pertama</p>
                            <button id="createFirstUserBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah User Pertama
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        users.forEach(function(user) {
            // Format kapal data
            const kapalList = user.kapals && user.kapals.length > 0 ?
                user.kapals.map(kapal => kapal.nama_kapal).join(', ') :
                '-';

            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">Username : ${user.username}</div>
                            <div class="text-gray-500 dark:text-gray-400">Nama Lengkap : ${user.nama_lengkap || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">Email : ${user.email || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Role : ${user.group ? user.group.group : '-'}</div>
                            <div>UPT : ${user.upt ? user.upt.nama : '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Nip : ${user.nip || '-'}</div>
                            <div>Gol : ${user.golongan || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white max-w-xs">
                            <div class="break-words">${kapalList}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewUser(${user.conf_user_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="editUser(${user.conf_user_id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit User">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteUser(${user.conf_user_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus User">
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
            paginationHtml += `<button onclick="loadUsers(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Sebelumnya
            </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadUsers(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'} border rounded-md">
                ${i}
            </button>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadUsers(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Selanjutnya
            </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    // Function to show error message
    function showErrorMessage(message) {
        $('#usersTableBody').html(`
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
    function editUser(id) {
        $.ajax({
            url: `/users/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const user = response.user;
                    $('#modalTitle').text('Form Edit User');
                    $('#userId').val(user.conf_user_id);
                    $('#username').val(user.username);
                    $('#nama_lengkap').val(user.nama_lengkap);
                    $('#email').val(user.email);
                    $('#modalRole').val(user.conf_group_id);
                    $('#modalUpt').val(user.m_upt_code);
                    $('#nip').val(user.nip);
                    $('#golongan').val(user.golongan);
                    $('#password').attr('required', false);
                    $('#passwordHelpText').removeClass('hidden'); // Show help text for edit mode
                    $('#userModal').removeClass('hidden').addClass('flex items-center justify-center');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    toastr.error('Terjadi kesalahan saat mengambil data user');
                }
            }
        });
    }

    function viewUser(id) {
        $.ajax({
            url: `/users/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const user = response.user;
                    const userDetails = `
                    <div class="space-y-4">
                        <div class="mb-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">${user.nama_lengkap || '-'}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@${user.username}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-bold text-gray-700 dark:text-gray-300">Email:</span>
                                <p class="text-gray-900 dark:text-white">${user.email || '-'}</p>
                            </div>
                            <div>
                                <span class="font-bold text-gray-700 dark:text-gray-300">Role:</span>
                                <p class="text-gray-900 dark:text-white">${user.group ? user.group.group : '-'}</p>
                            </div>
                            <div>
                                <span class="font-bold text-gray-700 dark:text-gray-300">UPT:</span>
                                <p class="text-gray-900 dark:text-white">${user.upt ? user.upt.nama : '-'}</p>
                            </div>
                            <div>
                                <span class="font-bold text-gray-700 dark:text-gray-300">NIP:</span>
                                <p class="text-gray-900 dark:text-white">${user.nip || '-'}</p>
                            </div>
                            <div>
                                <span class="font-bold text-gray-700 dark:text-gray-300">Golongan:</span>
                                <p class="text-gray-900 dark:text-white">${user.golongan || '-'}</p>
                            </div>
                        </div>
                    </div>
                `;
                    $('#userDetails').html(userDetails);
                    $('#viewUserModal').removeClass('hidden');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    toastr.error('Terjadi kesalahan saat mengambil data user');
                }
            }
        });
    }

    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            $.ajax({
                url: `/users/${id}`
                , type: 'POST'
                , data: {
                    _method: 'DELETE'
                    , _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        loadUsers(currentPage); // Reload current page instead of page reload
                        toastr.success(response.message || 'User berhasil dihapus');
                    } else {
                        toastr.error(response.message || 'Gagal menghapus user');
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

</script>
@endsection

@section('modals')
<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-white">Form Tambah User</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="userForm" class="mt-6">
                @csrf
                <input type="hidden" id="userId" name="user_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Username</label>
                            <input type="text" id="username" name="username" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="modalRole" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Role</label>
                            <select id="modalRole" name="group_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">- PILIH -</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->conf_group_id }}">{{ $group->group }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="nama_lengkap" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="golongan" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Golongan</label>
                            <input type="text" id="golongan" name="golongan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="modalUpt" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">UPT</label>
                            <select id="modalUpt" name="upt_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">- PILIH -</option>
                                @foreach($upts as $upt)
                                <option value="{{ $upt->code }}">{{ $upt->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="nip" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                            <input type="text" id="nip" name="nip" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <p id="passwordHelpText" class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden">Kosongkan jika tidak ingin mengubah password</p>
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

<!-- View User Modal -->
<div id="viewUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail User</h3>
                <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="userDetails" class="mt-6">
                <!-- User details will be loaded here -->
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
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan Manajemen User</h3>
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
                        Halaman ini digunakan untuk mengelola data pengguna sistem SIGOTIK. Anda dapat menambah, mengedit, melihat detail, dan menghapus user.
                    </p>
                </div>

                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔧 Fitur Utama</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">➕ Tambah User</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol "Tambah User" untuk menambahkan pengguna baru ke sistem.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔍 Pencarian & Filter</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Gunakan kolom pencarian dan filter untuk menemukan user tertentu.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">👁️ Lihat Detail</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon mata untuk melihat detail lengkap user.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">✏️ Edit User</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon pensil untuk mengedit data user.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📝 Field Form User</h4>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">1</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Username</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Nama pengguna untuk login (wajib diisi)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">2</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Nama Lengkap</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Nama lengkap pengguna (wajib diisi)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">3</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Email</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Alamat email pengguna (wajib diisi)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">4</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Role</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Grup/role pengguna dalam sistem (wajib diisi)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">5</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">UPT</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Unit Pelaksana Teknis tempat user bekerja</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">6</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Password</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Kata sandi untuk login (wajib untuk user baru)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">💡 Tips</h4>
                    <ul class="text-yellow-800 dark:text-yellow-200 space-y-1">
                        <li>• Pastikan username unik untuk setiap user</li>
                        <li>• Password minimal 6 karakter untuk keamanan</li>
                        <li>• Pilih role yang sesuai dengan tugas user</li>
                        <li>• User dapat memiliki akses ke beberapa kapal</li>
                        <li>• Gunakan filter untuk mencari user dengan cepat</li>
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
