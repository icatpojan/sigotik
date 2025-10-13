@extends('layouts.dashboard')

@section('title', 'Manajemen Kapal')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kapal</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola data kapal dan spesifikasi teknis</p>
        </div>
        <div class="flex gap-2">
            <button id="helpBtn" class="inline-flex items-center px-4 py-2 text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 dark:border-green-700 dark:hover:border-green-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </button>
            <button id="createKapalBtn" class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Kapal
            </button>
        </div>
    </div>

    <!-- Filter and Kapals Table in One Card -->
    <div class="bg-white dark:bg-gray-800 rounded-none border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
        <!-- Filter Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4 pb-4">
            <form id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full sm:w-40">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Kapal</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Cari nama kapal..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ request('search') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
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

                <!-- Tahun Buat Filter -->
                <div class="w-full sm:w-40">
                    <label for="tahun_buat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Buat</label>
                    <select id="tahun_buat" name="tahun_buat" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Tahun</option>
                        @for($year = date('Y'); $year >= 1990; $year--)
                        <option value="{{ $year }}" {{ request('tahun_buat') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
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

        <!-- Kapals Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-600" style="border-radius:20%">
                <thead style="background-color: #568fd2;">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Nama & Kode Kapal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">UPT & Spesifikasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Mesin & Kapasitas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Petugas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider border border-gray-300 dark:border-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kapalsTableBody" class="bg-white dark:bg-gray-800">
                    <!-- Data akan dimuat via AJAX -->
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                            <div class="flex flex-col items-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data kapal</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan kapal pertama</p>
                                <button id="createFirstKapalBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Kapal Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
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
@endsection

@section('modals')
<!-- Kapal Modal -->
<div id="kapalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-5 mb-5 max-h-[95vh] overflow-y-auto">
        <div class="mt-3">
            <!-- Modal Header - Sticky -->
            <div class="flex items-center justify-between pb-4 sticky top-0 bg-white dark:bg-gray-800 z-10 border-b border-gray-200 dark:border-gray-700 mb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-white">Form Tambah Kapal</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="kapalForm" class="mt-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="kapalId" name="kapal_id">

                <div class="space-y-6">
                    <!-- Informasi Umum Kapal -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Umum Kapal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_kapal" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Kapal</label>
                                <input type="text" id="nama_kapal" name="nama_kapal" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="code_kapal" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kode Kapal</label>
                                <input type="text" id="code_kapal" name="code_kapal" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="m_upt_code" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">UPT</label>
                                <select id="m_upt_code" name="m_upt_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">- PILIH -</option>
                                    @foreach($upts as $upt)
                                    <option value="{{ $upt->code }}">{{ $upt->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="tahun_buat" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tahun Buat</label>
                                <input type="number" id="tahun_buat" name="tahun_buat" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Dimensi Kapal -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dimensi Kapal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="panjang" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Panjang</label>
                                <input type="number" step="0.01" id="panjang" name="panjang" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="tinggi" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tinggi</label>
                                <input type="number" step="0.01" id="tinggi" name="tinggi" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="lebar" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Lebar</label>
                                <input type="number" step="0.01" id="lebar" name="lebar" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="bobot" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Bobot</label>
                                <input type="number" step="0.01" id="bobot" name="bobot" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Mesin Induk -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Mesin Induk</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="main_engine" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mesin Induk</label>
                                <input type="text" id="main_engine" name="main_engine" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="jml_main_engine" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah Mesin Induk</label>
                                <input type="number" id="jml_main_engine" name="jml_main_engine" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="pk_main_engine" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">PK Mesin Induk</label>
                                <input type="text" id="pk_main_engine" name="pk_main_engine" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Mesin Bantu -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Mesin Bantu</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="aux_engine_utama" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mesin Bantu Utama</label>
                                <input type="text" id="aux_engine_utama" name="aux_engine_utama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="jml_aux_engine_utama" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah Mesin Bantu Utama</label>
                                <input type="number" id="jml_aux_engine_utama" name="jml_aux_engine_utama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="pk_aux_engine_utama" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">PK Mesin Bantu Utama</label>
                                <input type="text" id="pk_aux_engine_utama" name="pk_aux_engine_utama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="gerak_engine" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mesin Penggerak (MTU)</label>
                                <input type="text" id="gerak_engine" name="gerak_engine" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="aux_engine_emergency" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mesin Bantu Emergency</label>
                                <input type="text" id="aux_engine_emergency" name="aux_engine_emergency" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Kapasitas dan Konstruksi -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Kapasitas dan Konstruksi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="kapasitas_tangki" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kapasitas Tangki BBM</label>
                                <input type="text" id="kapasitas_tangki" name="kapasitas_tangki" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="jml_tangki" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah Tangki</label>
                                <input type="number" id="jml_tangki" name="jml_tangki" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="galangan_pembuat" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Galangan Pembuat</label>
                                <input type="text" id="galangan_pembuat" name="galangan_pembuat" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="jml_abk" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah ABK</label>
                                <input type="number" id="jml_abk" name="jml_abk" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Detail Nakhoda -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Nakhoda</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="nama_nakoda" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Nakhoda</label>
                                <input type="text" id="nama_nakoda" name="nama_nakoda" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="nip_nakoda" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nip Nakhoda</label>
                                <input type="text" id="nip_nakoda" name="nip_nakoda" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="jabatan_nakoda" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jabatan Nakhoda</label>
                                <input type="text" id="jabatan_nakoda" name="jabatan_nakoda" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="pangkat_nakoda" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pangkat Nakhoda</label>
                                <input type="text" id="pangkat_nakoda" name="pangkat_nakoda" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="golongan_nakoda" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Golongan Nakhoda</label>
                                <input type="text" id="golongan_nakoda" name="golongan_nakoda" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Detail KKM -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail KKM</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="nama_kkm" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama KKM</label>
                                <input type="text" id="nama_kkm" name="nama_kkm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="nip_kkm" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nip KKM</label>
                                <input type="text" id="nip_kkm" name="nip_kkm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="jabatan_kkm" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jabatan KKM</label>
                                <input type="text" id="jabatan_kkm" name="jabatan_kkm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="pangkat_kkm" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pangkat KKM</label>
                                <input type="text" id="pangkat_kkm" name="pangkat_kkm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="golongan_kkm" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Golongan KKM</label>
                                <input type="text" id="golongan_kkm" name="golongan_kkm" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dokumen dan Gambar</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="gambar_kapal" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Gambar Kapal</label>
                                <input type="file" id="gambar_kapal" name="gambar_kapal" accept=".jpg,.jpeg,.png" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG (Max: 2MB)</p>
                                <div id="current-gambar" class="mt-2 hidden">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">File saat ini:</p>
                                    <a href="#" id="current-gambar-link" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Lihat gambar</a>
                                </div>
                            </div>
                            <div>
                                <label for="lampiran_kapal" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Lampiran Kapal</label>
                                <input type="file" id="lampiran_kapal" name="lampiran_kapal" accept=".pdf" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PDF (Max: 5MB)</p>
                                <div id="current-lampiran" class="mt-2 hidden">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">File saat ini:</p>
                                    <a href="#" id="current-lampiran-link" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Lihat dokumen</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer - Sticky -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 sticky bottom-0 bg-white dark:bg-gray-800 z-10">
                    <button type="submit" id="submitBtn" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Kapal Modal -->
<div id="viewKapalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[99999]">
    <div class="relative mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-none bg-white dark:bg-gray-800 mt-5 mb-5 max-h-[95vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Kapal</h3>
                <button id="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="kapalDetails" class="mt-6">
                <!-- Kapal details will be loaded here -->
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
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Panduan Manajemen Kapal</h3>
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
                        Halaman ini untuk mengelola data kapal dan spesifikasi teknis. Anda dapat menambah, mengedit, melihat detail, dan menghapus data kapal.
                    </p>
                </div>

                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">🔧 Fitur Utama</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🔍 Pencarian & Filter</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Cari kapal berdasarkan nama kapal. Gunakan filter untuk menemukan data dengan cepat.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">➕ Tambah Kapal</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol "Tambah Kapal" untuk menambahkan data kapal baru.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">✏️ Edit Kapal</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol edit untuk mengubah data kapal yang sudah ada.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">👁️ Lihat Detail</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol detail untuk melihat informasi lengkap kapal.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🗑️ Hapus Kapal</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Klik tombol hapus untuk menghapus data kapal (dengan konfirmasi).
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📄 Pagination</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Navigasi halaman untuk melihat data lebih banyak.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📝 Field Form Kapal</h4>
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">🚢 Informasi Kapal</h5>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• <strong>Nama Kapal:</strong> Nama lengkap kapal</li>
                                <li>• <strong>Kode Kapal:</strong> Kode unik untuk identifikasi kapal</li>
                                <li>• <strong>Jenis Kapal:</strong> Kategori atau jenis kapal</li>
                                <li>• <strong>Ukuran Kapal:</strong> Dimensi atau ukuran kapal</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">👤 Informasi Nahkoda & KKM</h5>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• <strong>Nama Nahkoda:</strong> Nama nahkoda yang bertanggung jawab</li>
                                <li>• <strong>Nama KKM:</strong> Nama KKM (Kepala Kamar Mesin)</li>
                                <li>• <strong>Golongan KKM:</strong> Golongan atau pangkat KKM</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">📸 Upload Gambar</h5>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• <strong>Gambar Kapal:</strong> Upload foto atau gambar kapal</li>
                                <li>• <strong>Format:</strong> JPG, PNG, atau format gambar lainnya</li>
                                <li>• <strong>Ukuran:</strong> Sesuaikan dengan kebutuhan sistem</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg">
                    <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">💡 Tips</h4>
                    <ul class="text-yellow-800 dark:text-yellow-200 space-y-1">
                        <li>• Gunakan pencarian untuk menemukan kapal tertentu dengan cepat</li>
                        <li>• Pastikan kode kapal unik dan tidak duplikat</li>
                        <li>• Isi semua field yang wajib diisi (bertanda *)</li>
                        <li>• Upload gambar kapal untuk identifikasi yang lebih baik</li>
                        <li>• Gunakan pagination untuk melihat data lebih banyak</li>
                        <li>• Klik detail untuk melihat informasi lengkap kapal</li>
                        <li>• Hapus data dengan hati-hati karena tidak dapat dibatalkan</li>
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
    // Configure Toastr
    toastr.options = {
        "closeButton": true
        , "debug": false
        , "newestOnTop": false
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

</script>

<script>
    let currentPage = 1;
    let currentFilters = {};

    $(document).ready(function() {
        // Load initial data
        loadKapals();

        // Help button
        $('#helpBtn').click(function() {
            $('#helpModal').removeClass('hidden').addClass('flex items-center justify-center');
        });

        // Help modal controls
        $('#closeHelpModal, #closeHelpModalBtn').click(function() {
            $('#helpModal').addClass('hidden').removeClass('flex items-center justify-center');
        });

        // Modal controls
        $('#createKapalBtn, #createFirstKapalBtn').click(function() {
            $('#modalTitle').text('Form Tambah Kapal');
            $('#kapalForm')[0].reset();
            $('#kapalId').val('');

            // Hide current files for new kapal
            $('#current-gambar').addClass('hidden');
            $('#current-lampiran').addClass('hidden');

            $('#kapalModal').removeClass('hidden');
            // Scroll to top of modal
            $('#kapalModal').scrollTop(0);
        });

        $('#closeModal').click(function() {
            $('#kapalModal').addClass('hidden');
        });

        $('#closeViewModal').click(function() {
            $('#viewKapalModal').addClass('hidden');
        });

        // Filter form submission
        $('#filterForm').on('change', 'select, input', function() {
            currentPage = 1; // Reset to first page when filtering
            loadKapals();
        });

        // Clear filter button
        $('#clearFilter').click(function() {
            $('#search').val('');
            $('#upt').val('');
            $('#tahun_buat').val('');
            $('#perPage').val('10');
            currentPage = 1;
            currentFilters = {};
            loadKapals();
        });

        // Kapal form submission
        $('#kapalForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const kapalId = $('#kapalId').val();
            const url = kapalId ? `/kapals/${kapalId}` : '/kapals';
            const method = kapalId ? 'PUT' : 'POST';

            // Add _method for PUT request
            if (kapalId) {
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
                        toastr.success(response.message);
                        $('#kapalModal').addClass('hidden');
                        loadKapals(); // Reload data instead of page reload
                    } else {
                        toastr.error(response.message);
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
                        toastr.error(errorMessage);
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });
    });

    // Function to load Kapals data via AJAX
    function loadKapals(page = 1) {
        currentPage = page;

        // Show loading indicator
        $('#loadingIndicator').removeClass('hidden').addClass('flex');
        $('#kapalsTableBody').html('');
        $('#paginationContainer').html('');

        // Get current filter values
        const filters = {
            search: $('#search').val()
            , upt: $('#upt').val()
            , tahun_buat: $('#tahun_buat').val()
            , per_page: $('#perPage').val()
            , page: page
        };

        currentFilters = filters;

        $.ajax({
            url: '/kapals/data'
            , type: 'GET'
            , data: filters
            , success: function(response) {
                if (response.success) {
                    renderKapalsTable(response.kapals);
                    renderPagination(response.pagination, response.links);
                } else {
                    showErrorMessage('Gagal memuat data kapal');
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

    // Function to render Kapals table
    function renderKapalsTable(kapals) {
        const tbody = $('#kapalsTableBody');
        tbody.html('');

        if (kapals.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                        <div class="flex flex-col items-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada data kapal</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan kapal pertama</p>
                            <button id="createFirstKapalBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Kapal Pertama
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        kapals.forEach(function(kapal) {
            const row = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">${kapal.nama_kapal}</div>
                            <div class="text-gray-500 dark:text-gray-400">Kode: ${kapal.code_kapal || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>UPT: ${kapal.upt ? kapal.upt.nama : '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">Tahun: ${kapal.tahun_buat || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Mesin: ${kapal.main_engine || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">Kapasitas: ${kapal.kapasitas_tangki || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div>Nakhoda: ${kapal.nama_nakoda || '-'}</div>
                            <div class="text-gray-500 dark:text-gray-400">KKM: ${kapal.nama_kkm || '-'}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium border border-gray-300 dark:border-gray-600">
                        <div class="flex items-center justify-end space-x-1">
                            <button onclick="viewKapal(${kapal.m_kapal_id})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 dark:border-blue-700 dark:hover:border-blue-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="editKapal(${kapal.m_kapal_id})" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 hover:border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 dark:border-yellow-700 dark:hover:border-yellow-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit Kapal">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteKapal(${kapal.m_kapal_id})" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 dark:border-red-700 dark:hover:border-red-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus Kapal">
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
            paginationHtml += `<button onclick="loadKapals(${pagination.current_page - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Sebelumnya
            </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === pagination.current_page;
            paginationHtml += `<button onclick="loadKapals(${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'} border rounded-md">
                ${i}
            </button>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<button onclick="loadKapals(${pagination.current_page + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Selanjutnya
            </button>`;
        }

        paginationHtml += '</div></div>';
        container.html(paginationHtml);
    }

    // Function to show error message
    function showErrorMessage(message) {
        $('#kapalsTableBody').html(`
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
    function editKapal(id) {
        $.ajax({
            url: `/kapals/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const kapal = response.kapal;
                    $('#modalTitle').text('Form Edit Kapal');
                    $('#kapalId').val(kapal.m_kapal_id);
                    $('#nama_kapal').val(kapal.nama_kapal);
                    $('#code_kapal').val(kapal.code_kapal);
                    $('#m_upt_code').val(kapal.m_upt_code);
                    $('#tahun_buat').val(kapal.tahun_buat);
                    $('#panjang').val(kapal.panjang);
                    $('#tinggi').val(kapal.tinggi);
                    $('#lebar').val(kapal.lebar);
                    $('#bobot').val(kapal.bobot);
                    $('#main_engine').val(kapal.main_engine);
                    $('#jml_main_engine').val(kapal.jml_main_engine);
                    $('#pk_main_engine').val(kapal.pk_main_engine);
                    $('#aux_engine_utama').val(kapal.aux_engine_utama);
                    $('#jml_aux_engine_utama').val(kapal.jml_aux_engine_utama);
                    $('#pk_aux_engine_utama').val(kapal.pk_aux_engine_utama);
                    $('#gerak_engine').val(kapal.gerak_engine);
                    $('#aux_engine_emergency').val(kapal.aux_engine_emergency);
                    $('#galangan_pembuat').val(kapal.galangan_pembuat);
                    $('#kapasitas_tangki').val(kapal.kapasitas_tangki);
                    $('#jml_tangki').val(kapal.jml_tangki);
                    $('#jml_abk').val(kapal.jml_abk);
                    $('#nama_nakoda').val(kapal.nama_nakoda);
                    $('#nip_nakoda').val(kapal.nip_nakoda);
                    $('#jabatan_nakoda').val(kapal.jabatan_nakoda);
                    $('#pangkat_nakoda').val(kapal.pangkat_nakoda);
                    $('#golongan_nakoda').val(kapal.golongan_nakoda);
                    $('#nama_kkm').val(kapal.nama_kkm);
                    $('#nip_kkm').val(kapal.nip_kkm);
                    $('#jabatan_kkm').val(kapal.jabatan_kkm);
                    $('#pangkat_kkm').val(kapal.pangkat_kkm);
                    $('#golongan_kkm').val(kapal.golongan_kkm);

                    // Show current files if they exist
                    if (kapal.gambar_kapal) {
                        $('#current-gambar').removeClass('hidden');
                        $('#current-gambar-link').attr('href', '/' + kapal.gambar_kapal);
                    } else {
                        $('#current-gambar').addClass('hidden');
                    }

                    if (kapal.lampiran_kapal) {
                        $('#current-lampiran').removeClass('hidden');
                        $('#current-lampiran-link').attr('href', '/' + kapal.lampiran_kapal);
                    } else {
                        $('#current-lampiran').addClass('hidden');
                    }

                    $('#kapalModal').removeClass('hidden');
                    // Scroll to top of modal
                    $('#kapalModal').scrollTop(0);
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    toastr.error('Terjadi kesalahan saat mengambil data kapal');
                }
            }
        });
    }

    function viewKapal(id) {
        $.ajax({
            url: `/kapals/${id}`
            , type: 'GET'
            , success: function(response) {
                if (response.success) {
                    const kapal = response.kapal;
                    const kapalDetails = `
                    <div class="space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Dasar</h5>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Nama Kapal:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.nama_kapal}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Kode Kapal:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.code_kapal || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">UPT:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.upt ? kapal.upt.nama : '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tahun Buat:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.tahun_buat || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Dimensi:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.panjang || '-'} x ${kapal.lebar || '-'} x ${kapal.tinggi || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Bobot:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.bobot || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Kapasitas Tangki:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.kapasitas_tangki || '-'}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Files Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dokumen dan Gambar</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h6 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Gambar Kapal</h6>
                                    ${kapal.gambar_kapal ?
                                        `<div class="space-y-2">
                                            <img src="/${kapal.gambar_kapal}" alt="Gambar Kapal" class="w-full h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                            <a href="/${kapal.gambar_kapal}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Lihat Gambar
                                            </a>
                                        </div>` :
                                        `<p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada gambar</p>`
                                    }
                                </div>
                                <div>
                                    <h6 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Lampiran Kapal</h6>
                                    ${kapal.lampiran_kapal ?
                                        `<div class="space-y-2">
                                            <div class="flex items-center p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                                <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Dokumen Kapal</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF Document</p>
                                                </div>
                                            </div>
                                            <a href="/${kapal.lampiran_kapal}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Download Dokumen
                                            </a>
                                        </div>` :
                                        `<p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada lampiran</p>`
                                    }
                                </div>
                            </div>
                        </div>

                        <!-- Engine Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Mesin</h5>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Mesin Induk:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.main_engine || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Jumlah Mesin:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.jml_main_engine || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">PK Mesin:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.pk_main_engine || '-'}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Mesin Bantu:</span>
                                    <p class="text-gray-900 dark:text-white">${kapal.aux_engine_utama || '-'}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Personnel Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Petugas</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <h6 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Nakhoda</h6>
                                    <p class="text-gray-900 dark:text-white">${kapal.nama_nakoda || '-'}</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">${kapal.jabatan_nakoda || ''} ${kapal.pangkat_nakoda || ''}</p>
                                </div>
                                <div>
                                    <h6 class="font-medium text-gray-700 dark:text-gray-300 mb-2">KKM</h6>
                                    <p class="text-gray-900 dark:text-white">${kapal.nama_kkm || '-'}</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">${kapal.jabatan_kkm || ''} ${kapal.pangkat_kkm || ''}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    $('#kapalDetails').html(kapalDetails);
                    $('#viewKapalModal').removeClass('hidden');
                }
            }
            , error: function(xhr) {
                if (xhr.status === 401) {
                    // Session expired, redirect to login
                    window.location.href = '/login';
                } else {
                    toastr.error('Terjadi kesalahan saat mengambil data kapal');
                }
            }
        });
    }

    function deleteKapal(id) {
        if (confirm('Apakah Anda yakin ingin menghapus kapal ini?')) {
            $.ajax({
                url: `/kapals/${id}`
                , type: 'POST'
                , data: {
                    _method: 'DELETE'
                    , _token: $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        loadKapals(currentPage); // Reload current page instead of page reload
                    } else {
                        toastr.error(response.message);
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
