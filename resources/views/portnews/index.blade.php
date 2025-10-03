@extends('layouts.dashboard')

@section('title', 'Manajemen Berita Pelabuhan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Berita Pelabuhan</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola data berita dan informasi pelabuhan</p>
        </div>
        <button id="createPortNewsBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Berita
        </button>
    </div>

    <!-- Filter and Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Berita</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari berita..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="w-full sm:w-40">
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Kategori</label>
                    <select id="category" name="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-32">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
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

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Gambar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Judul & Isi Berita</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Author & Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="portNewsTableBody" class="bg-white dark:bg-gray-800">
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
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    let currentPage = 1;
    let currentFilters = {};
    let editorInstance;

    $(document).ready(function() {
        // Initialize CKEditor 5
        try {
            ClassicEditor
                .create(document.querySelector('#news-editor'), {
                    toolbar: ['bold', 'italic', 'underline', 'bulletedList', 'numberedList', 'blockQuote', 'link', 'imageUpload']
                })
                .then(editor => {
                    editorInstance = editor;

                    // Sync editor content with textarea
                    editor.model.document.on('change:data', () => {
                        $('#news').val(editor.getData());
                    });
                })
                .catch(error => {
                    console.log('CKEditor 5 initialization failed:', error);
                });
        } catch (e) {
            console.log('CKEditor 5 initialization failed:', e);
        }

        // Load initial data
        loadPortNews();

        // Modal controls
        $('#createPortNewsBtn, #createFirstPortNewsBtn').click(function() {
            $('#modalTitle').text('Form Tambah Berita');
            $('#portNewsForm')[0].reset();
            $('#portNewsId').val('');
            $('#imagePreview').addClass('hidden');
            $('#currentImageContainer').addClass('hidden');
            if (editorInstance) {
                editorInstance.setData('');
            }
            $('#portNewsModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        $('#closeModal').click(function() {
            $('#portNewsModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        $('#closeViewModal').click(function() {
            $('#viewPortNewsModal').addClass('hidden');
        });

        // Preview image before upload
        $('#img').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').removeClass('hidden').find('img').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1;
            loadPortNews();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#category').val('');
            $('#status').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadPortNews();
        });

        // Form submission
        $('#portNewsForm').on('submit', function(e) {
            e.preventDefault();

            // Content is already synced automatically

            const formData = new FormData(this);
            const portNewsId = $('#portNewsId').val();
            const url = portNewsId ? `/portnews/${portNewsId}` : '/portnews';

            if (portNewsId) {
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
                        $('#portNewsModal').addClass('hidden');
                        loadPortNews();
                    } else {
                        alert(response.message);
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
                        }
                        alert(errorMessage);
                    } else {
                        alert('Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });
    });

    // Function to load port news data via AJAX
    function loadPortNews(page = 1) {
        currentPage = page;

        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#portNewsTableBody').html('');
        $('#paginationContainer').html('');

        const filters = {
            search: $('#search').val() || ''
            , category: $('#category').val() || ''
            , status: $('#status').val() || ''
            , per_page: $('#perPage').val() || '10'
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/portnews/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderPortNewsTable(response.portnews);
                    renderPagination(response.pagination);
                } else {
                    showErrorMessage('Gagal memuat data berita');
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

    // Function to render table
    function renderPortNewsTable(portNews) {
        const tbody = $('#portNewsTableBody');
        tbody.html('');

        if (portNews.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data berita</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan berita pertama</p>
                            <button id="createFirstPortNewsBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Berita Pertama
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        portNews.forEach(function(item) {
            const imageUrl = item.img ? `/${item.img}` : '/images/logo-kkp.png';
            const newsPreview = stripHtml(item.news).substring(0, 100) + '...';
            const statusBadge = item.post == '1' ?
                '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">Published</span>' :
                '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-200">Draft</span>';

            const dateFormatted = item.date_create ? new Date(item.date_create).toLocaleDateString('id-ID', {
                year: 'numeric'
                , month: 'long'
                , day: 'numeric'
            }) : '-';

            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <img src="${imageUrl}" alt="${item.news_title}" class="w-20 h-20 object-cover rounded" onerror="this.src='/images/logo-kkp.png'">
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium mb-1">${item.news_title}</div>
                            <div class="text-gray-500 dark:text-gray-400 text-xs">${newsPreview}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600 text-center">
                        <span class="text-sm text-gray-900 dark:text-white">${item.category ? item.category.name : '-'}</span>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Author: ${item.author || '-'}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${dateFormatted}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600 text-center">
                        ${statusBadge}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewPortNews(${item.id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="editPortNews(${item.id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit Berita">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deletePortNews(${item.id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus Berita">
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
    function renderPagination(pagination) {
        const container = $('#paginationContainer');

        if (pagination.last_page <= 1) {
            container.html('');
            return;
        }

        let paginationHtml = '<div class="flex items-center justify-between">';

        paginationHtml += `<div class="text-sm text-gray-700 dark:text-gray-300">
            Menampilkan ${pagination.from || 0} sampai ${pagination.to || 0} dari ${pagination.total} data (${pagination.per_page} per halaman)
        </div>`;

        paginationHtml += '<div class="flex space-x-1">';

        if (pagination.current_page > 1) {
            paginationHtml += `<button onclick="loadPortNews(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Sebelumnya
            </button>`;
        }

        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadPortNews(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'} border rounded-md">
                ${i}
            </button>`;
        }

        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadPortNews(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Selanjutnya
            </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    function showErrorMessage(message) {
        $('#portNewsTableBody').html(`
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

    function stripHtml(html) {
        let tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }

    function editPortNews(id) {
        $.ajax({
            url: `/portnews/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const item = response.portnews;
                    $('#modalTitle').text('Form Edit Berita');
                    $('#portNewsId').val(item.id);
                    $('#news_title').val(item.news_title);
                    if (editorInstance) {
                        editorInstance.setData(item.news);
                    }
                    $('#kategori_id').val(item.kategori_id);
                    $('#author').val(item.author);
                    $('#post').val(item.post);

                    $('#imagePreview').addClass('hidden');
                    if (item.img) {
                        $('#currentImage').attr('src', `/${item.img}`);
                        $('#currentImageContainer').removeClass('hidden');
                    } else {
                        $('#currentImageContainer').addClass('hidden');
                    }

                    $('#portNewsModal').removeClass('hidden').addClass('flex items-center justify-center');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    window.location.href = '/login';
                } else {
                    alert('Terjadi kesalahan saat mengambil data berita');
                }
            }
        });
    }

    function viewPortNews(id) {
        $.ajax({
            url: `/portnews/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const item = response.portnews;
                    const imageUrl = item.img ? `/${item.img}` : '/images/logo-kkp.png';
                    const statusBadge = item.post == '1' ?
                        '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Published</span>' :
                        '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Draft</span>';

                    const dateFormatted = item.date_create ? new Date(item.date_create).toLocaleDateString('id-ID', {
                        year: 'numeric'
                        , month: 'long'
                        , day: 'numeric'
                    }) : '-';

                    const details = `
                        <div class="space-y-4">
                            <div class="flex justify-center">
                                <img src="${imageUrl}" alt="${item.news_title}" class="max-w-full h-48 object-cover rounded-lg" onerror="this.src='/images/logo-kkp.png'">
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${item.news_title}</h4>
                                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    <span>Kategori: ${item.category ? item.category.name : '-'}</span>
                                    <span>•</span>
                                    <span>Author: ${item.author || '-'}</span>
                                    <span>•</span>
                                    <span>${dateFormatted}</span>
                                    <span>•</span>
                                    ${statusBadge}
                                </div>
                                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                                    ${item.news}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#portNewsDetails').html(details);
                    $('#viewPortNewsModal').removeClass('hidden');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    window.location.href = '/login';
                } else {
                    alert('Terjadi kesalahan saat mengambil data berita');
                }
            }
        });
    }

    function deletePortNews(id) {
        if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
            $.ajax({
                url: `/portnews/${id}`
                , type: 'POST'
                , data: {
                    _method: 'DELETE'
                    , _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        loadPortNews(currentPage);
                    } else {
                        alert(response.message);
                    }
                }
                , error: function(xhr) {
                    if (xhr.status === 401) {
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
<!-- Port News Modal -->
<div id="portNewsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-3/4 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-white">Form Tambah Berita</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="portNewsForm" class="mt-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="portNewsId" name="portnews_id">

                <div class="space-y-4">
                    <!-- Judul Berita -->
                    <div>
                        <label for="news_title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Judul Berita</label>
                        <input type="text" id="news_title" name="news_title" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Kategori -->
                        <div>
                            <label for="kategori_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                            <select id="kategori_id" name="kategori_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">- PILIH -</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Author -->
                        <div>
                            <label for="author" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Author</label>
                            <input type="text" id="author" name="author" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Isi Berita -->
                    <div>
                        <label for="news" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Isi Berita</label>
                        <textarea id="news" name="news" rows="5" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" style="display: none;"></textarea>
                        <div id="news-editor" style="min-height: 300px;"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Status -->
                        <div>
                            <label for="post" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select id="post" name="post" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="1">Published</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>

                        <!-- Gambar -->
                        <div>
                            <label for="img" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Gambar</label>
                            <input type="file" id="img" name="img" accept="image/*" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG, GIF (Max: 2MB)</p>
                        </div>
                    </div>

                    <!-- Current Image Preview (for edit) -->
                    <div id="currentImageContainer" class="hidden">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Gambar Saat Ini</label>
                        <img id="currentImage" src="" alt="Current Image" class="w-32 h-32 object-cover rounded-lg">
                    </div>

                    <!-- New Image Preview -->
                    <div id="imagePreview" class="hidden">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Preview Gambar Baru</label>
                        <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Port News Modal -->
<div id="viewPortNewsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-3/4 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-20 max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Berita</h3>
                <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="portNewsDetails" class="mt-6">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection
