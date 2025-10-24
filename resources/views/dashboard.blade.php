@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<!-- Breadcrumb -->
{{-- <div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Dashboard</span>
                </div>
            </li>
        </ol>
    </nav>
</div> --}}

<!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Selamat Datang di SIGOTIK BBM</p>
    </div>
<!-- Period Info -->
<div class="mb-6">
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 dark:bg-green-900/20 dark:border-green-800">
        <div class="flex items-center">
            <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-200" id="period-info">
                Memuat periode pelaporan...
            </span>
        </div>
    </div>
</div>


<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="stats-cards">
    <!-- Loading Skeleton -->
    <div id="stats-loading" class="contents">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500 animate-pulse">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-24 mb-2"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32 mb-1"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                </div>
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500 animate-pulse">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-24 mb-2"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32 mb-1"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                </div>
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-yellow-500 animate-pulse">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-24 mb-2"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32 mb-1"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                </div>
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-red-500 animate-pulse">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-24 mb-2"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32 mb-1"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                </div>
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Perbandingan Anggaran dan Realisasi BBM</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400" id="chart-subtitle">Memuat data...</p>
        </div>
        <div class="p-6">
            <div id="chart-container" style="min-height: 400px;">
                <!-- Loading state -->
                <div class="flex items-center justify-center h-96">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600 dark:text-gray-400">Memuat chart...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Data Anggaran dan Realisasi per UPT</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Dalam Rupiah</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama UPT</th>
                        <th scope="col" class="px-6 py-3 text-right">Anggaran</th>
                        <th scope="col" class="px-6 py-3 text-right">Realisasi</th>
                        <th scope="col" class="px-6 py-3 text-right">Sisa Anggaran</th>
                        <th scope="col" class="px-6 py-3 text-right">Persentase</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- Loading state -->
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-2"></div>
                                Memuat data...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Function to format number with Indonesian locale
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Function to format currency
        function formatCurrency(num) {
            return 'Rp. ' + formatNumber(num);
        }

        // Function to load dashboard data
        async function loadDashboardData() {
            try {
                // Load all dashboard components
                await Promise.all([
                    loadStats()
                    , loadChartData()
                    , loadTableData()
                ]);

            } catch (error) {
                console.error('Error loading dashboard data:', error);
                showError('Gagal memuat data dashboard: ' + error.message);
            }
        }

        // Function to load stats
        async function loadStats() {
            try {
                const response = await fetch('{{ route("dashboard.stats") }}', {
                    method: 'GET'
                    , headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        , 'Accept': 'application/json'
                    , }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                if (result.success) {
                    updateStatsAndPeriod(result.data);
                } else {
                    throw new Error(result.message || 'Failed to load stats');
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                throw error;
            }
        }


        // Function to update stats and period info
        function updateStatsAndPeriod(data) {
            // Update period info
            document.getElementById('period-info').textContent =
                `Periode Pelaporan: ${data.periodeAwal} Sampai dengan ${data.periodeAkhir}`;

            // Update stats cards
            updateStatsCards(data.stats);
        }

        // Function to update stats cards
        function updateStatsCards(stats) {
            const statsCards = document.getElementById('stats-cards');
            const loadingElement = document.getElementById('stats-loading');

            // Remove loading skeleton
            loadingElement.remove();

            // Create stats cards HTML
            statsCards.innerHTML = `
            <!-- Total Penggunaan BBM -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            ${formatNumber(stats.penggunaan)}
                        </h3>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Total Penggunaan BBM
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">(Liter)</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pengisian BBM -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            ${formatNumber(stats.penerimaan)}
                        </h3>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Total Pengisian BBM
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">(Liter)</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Anggaran BBM -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            ${formatCurrency(stats.anggaran)}
                        </h3>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Total Anggaran BBM
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">(Rupiah)</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Realisasi BBM -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            ${formatCurrency(stats.realisasi)}
                        </h3>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Total Realisasi BBM
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">(Rupiah)</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        `;
        }


        // Function to load chart data
        async function loadChartData() {
            try {
                const response = await fetch('{{ route("dashboard.chart") }}', {
                    method: 'GET'
                    , headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        , 'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                if (result.success) {
                    updateChart(result.data);
                } else {
                    throw new Error(result.message || 'Failed to load chart data');
                }
            } catch (error) {
                console.error('Error loading chart data:', error);
                showChartError('Gagal memuat data chart');
            }
        }

        // Function to load table data
        async function loadTableData() {
            try {
                const response = await fetch('{{ route("dashboard.table") }}', {
                    method: 'GET'
                    , headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        , 'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                if (result.success) {
                    updateTable(result.data);
                } else {
                    throw new Error(result.message || 'Failed to load table data');
                }
            } catch (error) {
                console.error('Error loading table data:', error);
                showTableError('Gagal memuat data tabel');
            }
        }

        // Function to update chart
        function updateChart(data) {
            const chartContainer = document.getElementById('chart-container');
            const chartSubtitle = document.getElementById('chart-subtitle');

            // Update subtitle
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            chartSubtitle.textContent = `Periode ${year} (Dalam Rupiah)`;

            // Create chart using Chart.js (you can use any charting library)
            chartContainer.innerHTML = `
                <canvas id="budgetChart" width="400" height="200"></canvas>
            `;

            // Initialize Chart.js
            const ctx = document.getElementById('budgetChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar'
                , data: {
                    labels: data.labels
                    , datasets: [{
                        label: 'Anggaran'
                        , data: data.anggaran
                        , backgroundColor: 'rgba(54, 162, 235, 0.8)'
                        , borderColor: 'rgba(54, 162, 235, 1)'
                        , borderWidth: 1
                    }, {
                        label: 'Realisasi'
                        , data: data.realisasi
                        , backgroundColor: 'rgba(255, 99, 132, 0.8)'
                        , borderColor: 'rgba(255, 99, 132, 1)'
                        , borderWidth: 1
                    }]
                }
                , options: {
                    responsive: true
                    , maintainAspectRatio: false
                    , scales: {
                        y: {
                            beginAtZero: true
                            , ticks: {
                                callback: function(value) {
                                    return 'Rp. ' + formatNumber(value);
                                }
                            }
                        }
                    }
                    , plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp. ' + formatNumber(context.parsed.y);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Function to update table
        function updateTable(data) {
            const tableBody = document.getElementById('table-body');

            if (data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data
                        </td>
                    </tr>
                `;
                return;
            }

            let tableHTML = '';
            data.forEach(row => {
                tableHTML += `
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            ${row.nama}
                        </td>
                        <td class="px-6 py-4 text-right">
                            ${formatCurrency(row.anggaran)}
                        </td>
                        <td class="px-6 py-4 text-right">
                            ${formatCurrency(row.realisasi)}
                        </td>
                        <td class="px-6 py-4 text-right">
                            ${formatCurrency(row.sisa_anggaran)}
                        </td>
                        <td class="px-6 py-4 text-right">
                            ${row.persentase.toFixed(2)}%
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = tableHTML;
        }

        // Function to show chart error
        function showChartError(message) {
            document.getElementById('chart-container').innerHTML = `
                <div class="flex items-center justify-center h-96">
                    <div class="text-center">
                        <div class="text-red-500 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <p class="text-red-600 dark:text-red-400">${message}</p>
                    </div>
                </div>
            `;
        }

        // Function to show table error
        function showTableError(message) {
            document.getElementById('table-body').innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-red-600 dark:text-red-400">
                        ${message}
                    </td>
                </tr>
            `;
        }

        // Function to show error
        function showError(message) {
            // Update period info with error
            document.getElementById('period-info').textContent = message;
            document.getElementById('period-info').classList.add('text-red-600', 'dark:text-red-400');

            // Hide loading states
            document.getElementById('stats-loading').style.display = 'none';
        }

        // Load dashboard data on page load
        loadDashboardData();
    });

</script>
@endsection
