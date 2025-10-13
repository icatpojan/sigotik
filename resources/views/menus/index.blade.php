@extends('layouts.dashboard')

@section('title', 'Manajemen Menu')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Menu</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola menu sistem aplikasi</p>
        </div>
        <div class="flex gap-2">
            <button id="helpBtn" class="inline-flex items-center px-4 py-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </button>
            <button id="addMenuBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Menu
            </button>
        </div>
    </div>

    <!-- Filter and Menus Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Menu</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari menu..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('search') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Level Filter -->
                <div class="w-full sm:w-40">
                    <label for="levelFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level Menu</label>
                    <select id="levelFilter" name="level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Level</option>
                        <option value="1">Menu Utama</option>
                        <option value="2">Sub Menu</option>
                    </select>
                </div>

                <!-- Parent Filter -->
                <div class="w-full sm:w-40">
                    <label for="parentFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Parent Menu</label>
                    <select id="parentFilter" name="parent_menu" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Parent</option>
                        <option value="main">Menu Utama Saja</option>
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

        <!-- Menus Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">ID</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Menu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Level</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Parent</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Link</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Icon</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Urutan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="menuTableBody" class="bg-white dark:bg-gray-800">
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
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan Manajemen Menu</h3>
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
                        Halaman ini digunakan untuk mengelola menu sistem aplikasi SIGOTIK. Menu menentukan struktur navigasi dan akses yang tersedia untuk user.
                    </p>
                </div>

                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔧 Fitur Utama</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">➕ Tambah Menu</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol "Tambah Menu" untuk membuat menu baru.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔍 Pencarian & Filter</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Gunakan filter untuk mencari menu berdasarkan level, parent, dll.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">✏️ Edit Menu</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon pensil untuk mengedit menu.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🗑️ Hapus Menu</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik ikon trash untuk menghapus menu.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Menu Types -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📁 Jenis Menu</h4>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">1</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Menu Utama (Level 1)</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Menu utama yang muncul di sidebar, biasanya memiliki icon</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">2</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Sub Menu (Level 2)</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Menu yang berada di bawah menu utama, memiliki parent menu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📝 Field Form Menu</h4>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">1</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Menu Level</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Pilih level menu: Menu Utama (1) atau Sub Menu (2)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">2</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Parent Menu</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Hanya muncul untuk Sub Menu, pilih menu utama sebagai parent</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">3</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Menu Name</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Nama menu yang akan ditampilkan (wajib diisi)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">4</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Link</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">URL atau route yang akan dituju (wajib untuk Sub Menu)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">5</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Icon</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Class icon FontAwesome (wajib untuk Menu Utama)</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-medium">6</span>
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Urutan</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Urutan tampil menu (wajib diisi, angka)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">💡 Tips</h4>
                    <ul class="text-yellow-800 dark:text-yellow-200 space-y-1">
                        <li>• Menu Utama harus memiliki icon, Sub Menu harus memiliki link</li>
                        <li>• Urutan menu menentukan posisi tampil di sidebar</li>
                        <li>• Icon menggunakan format FontAwesome (contoh: fa fa-home)</li>
                        <li>• Link untuk Sub Menu harus sesuai dengan route yang ada</li>
                        <li>• Hapus menu dengan hati-hati, pastikan tidak ada yang menggunakan</li>
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
        console.log('Menu page loaded, initializing...');

        // Load menus on page load
        loadMenus();

        // Help button
        $('#helpBtn').click(function() {
            $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        // Load parent menus for dropdown
        loadParentMenus();

        // Add menu button click
        $('#addMenuBtn').click(function() {
            openMenuModal();
        });

        // Close modal button
        $('#closeMenuModal').click(function() {
            $('#menuModal').addClass('hidden').hide();
        });

        // Close modal when clicking outside
        $('#menuModal').click(function(e) {
            if (e.target === this) {
                $(this).addClass('hidden').hide();
            }
        });

        // Level change handler
        $('#level').change(function() {
            const level = $(this).val();
            const parentDiv = $('#parentMenuDiv');
            const parentSelect = $('#id_parentmenu');
            const iconDiv = $('#iconDiv');
            const iconInput = $('#icon');
            const linkInput = $('#linka');

            if (level == 1) {
                // Menu Utama - hide parent menu div, show icon div
                parentDiv.hide();
                parentSelect.val('');
                parentSelect.prop('disabled', true);

                iconDiv.show();
                iconInput.prop('required', true);
                linkInput.prop('required', false);
            } else if (level == 2) {
                // Sub Menu - show parent menu div, hide icon div
                parentDiv.show();
                parentSelect.prop('disabled', false);

                iconDiv.hide();
                iconInput.val('');
                iconInput.prop('required', false);
                linkInput.prop('required', true);
            } else {
                // No selection - hide both parent and icon divs
                parentDiv.hide();
                parentSelect.val('');
                parentSelect.prop('disabled', true);

                iconDiv.hide();
                iconInput.val('');
                iconInput.prop('required', false);
                linkInput.prop('required', false);
            }
        });

        // Menu form submission
        $('#menuForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const menuId = $('#menuId').val();
            const url = menuId ? `/menus/${menuId}` : '/menus';
            const method = menuId ? 'PUT' : 'POST';

            // Add _method for PUT request
            if (menuId) {
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
                        $('#menuModal').addClass('hidden').hide();
                        loadMenus(currentPage);
                        toastr.success(response.message || 'Menu berhasil disimpan');
                    } else {
                        toastr.error(response.message || 'Gagal menyimpan menu');
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
                        toastr.error('Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });

        // Filter functionality
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            currentFilters = {
                search: $('#search').val()
                , level: $('#levelFilter').val()
                , parent_menu: $('#parentFilter').val()
                , per_page: $('#perPage').val()
            };
            console.log('Filter form submitted:', currentFilters);
            loadMenus(1);
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#levelFilter').val('');
            $('#parentFilter').val('');
            $('#perPage').val('10');
            currentFilters = {
                search: ''
                , level: ''
                , parent_menu: ''
                , per_page: '10'
            };
            loadMenus(1);
        });

        // Real-time search with debounce
        let searchTimeout;
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const searchValue = $('#search').val();
                currentFilters = {
                    search: searchValue
                    , level: $('#levelFilter').val()
                    , parent_menu: $('#parentFilter').val()
                    , per_page: $('#perPage').val()
                };
                console.log('Search input changed:', searchValue);
                loadMenus(1);
            }, 500);
        });

        // Filter change handlers
        $('#levelFilter, #parentFilter, #perPage').change(function() {
            currentFilters = {
                search: $('#search').val()
                , level: $('#levelFilter').val()
                , parent_menu: $('#parentFilter').val()
                , per_page: $('#perPage').val()
            };
            console.log('Filter changed:', currentFilters);
            loadMenus(1);
        });

        // Function to load menus data
        function loadMenus(page = 1) {
            currentPage = page;

            // Show loading indicator
            $('#loadingIndicator').removeClass('hidden').addClass('flex');
            $('#menuTableBody').empty();

            const params = {
                page: page
                , length: $('#perPage').val() || 10
                , draw: page
            };

            // Only add filters if they have values
            if (currentFilters.search && currentFilters.search.trim() !== '') {
                params.search = currentFilters.search;
            }
            if (currentFilters.level && currentFilters.level !== '') {
                params.level = currentFilters.level;
            }
            if (currentFilters.parent_menu && currentFilters.parent_menu !== '') {
                params.parent_menu = currentFilters.parent_menu;
            }

            console.log('Loading menus with params:', params);
            console.log('Current filters:', currentFilters);

            $.ajax({
                url: '/menus/data'
                , type: 'GET'
                , data: params
                , success: function(response) {
                    console.log('Menu data response:', response);
                    $('#loadingIndicator').addClass('hidden').removeClass('flex');
                    renderMenuTable(response.data);
                    renderPagination(response);
                }
                , error: function(xhr) {
                    console.error('Error loading menus:', xhr);
                    $('#loadingIndicator').addClass('hidden').removeClass('flex');
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else {
                        toastr.error('Terjadi kesalahan saat memuat data menu: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText));
                    }
                }
            });
        }

        // Function to load parent menus
        function loadParentMenus() {
            $.ajax({
                url: '/menus/parent/all'
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        const parentSelect = $('#id_parentmenu');
                        parentSelect.empty().append('<option value="">-- Pilih --</option>');

                        response.data.forEach(function(menu) {
                            parentSelect.append(`<option value="${menu.id}">${menu.menu}</option>`);
                        });
                    }
                }
                , error: function() {
                    console.error('Gagal memuat parent menu');
                }
            });
        }

        // Function to render menu table
        function renderMenuTable(menus) {
            const tbody = $('#menuTableBody');
            tbody.empty();

            if (menus.length === 0) {
                tbody.append(`
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                                Tidak ada data menu
                            </td>
                        </tr>
                    `);
                return;
            }

            menus.forEach(function(menu) {
                const levelText = menu.level == 1 ? 'Menu Utama' : 'Sub Menu';
                const parentText = menu.parent ? menu.parent.menu : '-';
                const linkText = menu.linka || '-';
                const iconText = menu.icon || '-';

                tbody.append(`
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">${menu.id}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">${menu.menu}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">${levelText}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">${parentText}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">${linkText}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">${iconText}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">${menu.urutan}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium border border-gray-300 dark:border-gray-600">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="editMenu(${menu.id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit Menu">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteMenu(${menu.id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus Menu">
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

        // Function to render pagination
        function renderPagination(response) {
            const pagination = $('#paginationContainer');
            pagination.empty();

            const perPage = parseInt($('#perPage').val()) || 10;
            const totalRecords = response.recordsFiltered || response.recordsTotal || 0;
            const totalPages = Math.ceil(totalRecords / perPage);
            const currentPage = parseInt(response.draw) || 1;

            console.log('Rendering pagination:', {
                totalRecords
                , perPage
                , totalPages
                , currentPage
            });

            if (totalPages <= 1) {
                pagination.html(`<div class="text-sm text-gray-700 dark:text-gray-300">Menampilkan ${totalRecords} data</div>`);
                return;
            }

            let paginationHtml = '<div class="flex items-center justify-between">';
            paginationHtml += `<div class="text-sm text-gray-700 dark:text-gray-300">Menampilkan ${((currentPage - 1) * perPage) + 1} sampai ${Math.min(currentPage * perPage, totalRecords)} dari ${totalRecords} data</div>`;

            paginationHtml += '<div class="flex space-x-1">';

            // Previous button
            if (currentPage > 1) {
                paginationHtml += `<button onclick="loadMenus(${currentPage - 1})" class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500">Previous</button>`;
            }

            // Page numbers (show max 5 pages)
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                paginationHtml += `<button onclick="loadMenus(1)" class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500">1</button>`;
                if (startPage > 2) {
                    paginationHtml += `<span class="px-3 py-1 text-sm text-gray-500">...</span>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    paginationHtml += `<span class="px-3 py-1 text-sm bg-blue-600 text-white rounded">${i}</span>`;
                } else {
                    paginationHtml += `<button onclick="loadMenus(${i})" class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500">${i}</button>`;
                }
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHtml += `<span class="px-3 py-1 text-sm text-gray-500">...</span>`;
                }
                paginationHtml += `<button onclick="loadMenus(${totalPages})" class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500">${totalPages}</button>`;
            }

            // Next button
            if (currentPage < totalPages) {
                paginationHtml += `<button onclick="loadMenus(${currentPage + 1})" class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-500">Next</button>`;
            }

            paginationHtml += '</div></div>';
            pagination.html(paginationHtml);
        }

        // Function to open menu modal
        function openMenuModal(menu = null) {
            const modal = $('#menuModal');
            const form = $('#menuForm')[0];
            const title = $('#modalTitle');
            const submitBtn = $('#submitBtn');

            // Reset form
            form.reset();
            $('#menuId').val('');

            // Hide parent menu div and icon div initially
            $('#parentMenuDiv').hide();
            $('#id_parentmenu').prop('disabled', true);
            $('#iconDiv').hide();

            // Reset field requirements
            $('#linka').prop('required', false);
            $('#icon').prop('required', false);

            if (menu) {
                // Edit mode
                title.text('Edit Menu');
                submitBtn.text('Update');

                $('#menuId').val(menu.id);
                $('#id_parentmenu').val(menu.id_parentmenu);
                $('#level').val(menu.level);
                $('#menu').val(menu.menu);
                $('#linka').val(menu.linka);
                $('#icon').val(menu.icon);
                $('#urutan').val(menu.urutan);

                // Trigger level change to handle parent selection
                $('#level').trigger('change');
            } else {
                // Add mode
                title.text('Tambah Menu');
                submitBtn.text('Simpan');
            }

            modal.removeClass('hidden').removeAttr('style').show();
        }

        // Global functions for inline onclick handlers
        window.editMenu = function(id) {
            $.ajax({
                url: `/menus/${id}`
                , type: 'GET'
                , success: function(response) {
                    if (response.success) {
                        openMenuModal(response.data);
                    } else {
                        toastr.error(response.message || 'Gagal memuat data menu');
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else {
                        toastr.error('Terjadi kesalahan saat memuat data menu');
                    }
                }
            });
        };

        window.deleteMenu = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
                // Create FormData with CSRF token
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $.ajax({
                    url: `/menus/${id}`
                    , type: 'POST'
                    , data: formData
                    , processData: false
                    , contentType: false
                    , success: function(response) {
                        if (response.success) {
                            loadMenus(currentPage);
                            toastr.success(response.message || 'Menu berhasil dihapus');
                        } else {
                            toastr.error(response.message || 'Gagal menghapus menu');
                        }
                    }
                    , error: function(xhr) {
                        if (xhr.status === 401) {
                            window.location.href = '/login';
                        } else {
                            toastr.error('Terjadi kesalahan saat menghapus data');
                        }
                    }
                });
            }
        };

        window.loadMenus = loadMenus;

        // Help modal controls
        $('#closeHelpModal, #closeHelpModalBtn').click(function() {
            $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
        });
    });

</script>
@endsection


@section('modals')
<!-- Menu Modal -->
<div id="menuModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]" style="display: none;">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white dark:bg-gray-800 mt-5">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white" id="modalTitle">Tambah Menu</h3>
                <button id="closeMenuModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="menuForm" class="mt-6">
                @csrf
                <input type="hidden" id="menuId" name="menu_id">

                <div class="space-y-4">
                    <!-- Menu Level -->
                    <div>
                        <label for="level" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Menu Level
                        </label>
                        <select id="level" name="level" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">-- Pilih --</option>
                            <option value="1">Menu Utama</option>
                            <option value="2">Sub Menu</option>
                        </select>
                    </div>

                    <!-- Parent Menu -->
                    <div id="parentMenuDiv">
                        <label for="id_parentmenu" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Parent Menu
                        </label>
                        <select id="id_parentmenu" name="id_parentmenu" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>

                    <!-- Menu Name -->
                    <div>
                        <label for="menu" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Menu Name
                        </label>
                        <input type="text" id="menu" name="menu" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Masukkan nama menu">
                    </div>

                    <!-- Link -->
                    <div>
                        <label for="linka" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Link
                        </label>
                        <input type="text" id="linka" name="linka" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Masukkan link menu">
                    </div>

                    <!-- Icon -->
                    <div id="iconDiv">
                        <label for="icon" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Icon
                        </label>
                        <input type="text" id="icon" name="icon" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="fa fa icon">
                    </div>

                    <!-- Urutan -->
                    <div>
                        <label for="urutan" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Urutan
                        </label>
                        <input type="number" id="urutan" name="urutan" required min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Masukkan urutan menu">
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
@endsection
