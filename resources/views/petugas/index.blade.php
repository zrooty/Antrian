<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Petugas') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full text-sm font-bold border border-blue-200 dark:border-blue-800">
                    {{ $counter->name ?? 'Belum Ada Loket' }} ({{ $counter->code ?? '-' }})
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-end mb-4">
                <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-full shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>Terakhir diperbarui: <span id="last-update-time">{{ now()->format('H:i:s') }}</span></span>
                </div>
            </div>

            <div id="petugas-dashboard-container">
                @include('petugas.partials.dashboard-content')
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('petugas-dashboard-container');
                    const timeElement = document.getElementById('last-update-time');
                    
                    function fetchDashboardData() {
                        const urlParams = new URLSearchParams(window.location.search);
                        const page = urlParams.get('page') || 1;

                        fetch(`{{ route('petugas.data') }}?page=${page}`)
                            .then(response => response.text())
                            .then(html => {
                                if (html.trim().length > 0) {
                                    container.innerHTML = html;
                                    
                                    // Update timestamp
                                    const now = new Date();
                                    const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                                                   now.getMinutes().toString().padStart(2, '0') + ':' + 
                                                   now.getSeconds().toString().padStart(2, '0');
                                    timeElement.innerText = timeStr;
                                }
                            })
                            .catch(error => console.error("Update failed:", error));
                    }

                    // Polling setiap 5 detik
                    setInterval(fetchDashboardData, 5000);

                    // Intercept pagination clicks
                    document.addEventListener('click', function(e) {
                        const link = e.target.closest('#waiting-pagination-container a');
                        if (link) {
                            e.preventDefault();
                            const url = new URL(link.href);
                            const page = url.searchParams.get('page');
                            
                            // Update URL without reload
                            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=' + page;
                            window.history.pushState({path:newUrl},'',newUrl);
                            
                            // Fetch immediately
                            fetchDashboardData();
                        }
                    });
                });
            </script>
        </div>
    </div>
</x-app-layout>
