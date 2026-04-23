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
            
            <div id="alert-status" class="hidden mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/20 dark:text-green-400 rounded-md"></div>
            <div id="alert-error" class="hidden mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/20 dark:text-red-400 rounded-md"></div>

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
                    const alertStatus = document.getElementById('alert-status');
                    const alertError = document.getElementById('alert-error');
                    
                    function fetchDashboardData() {
                        const urlParams = new URLSearchParams(window.location.search);
                        const page = urlParams.get('page') || 1;

                        fetch(`{{ route('petugas.data') }}?page=${page}`)
                            .then(response => response.json())
                            .then(data => {
                                // 1. Update Active Queue
                                document.getElementById('active-queue-container').innerHTML = data.activeQueueHtml;
                                
                                // 2. Update Waiting List (JSON Approach)
                                renderWaitingList(data.waitingQueues);
                                updateWaitingCount(data.totalWaiting);
                                updatePagination(data.pagination);
                                
                                // 3. Update Skipped & Handled
                                document.getElementById('skipped-list-container').innerHTML = data.skippedQueuesHtml;
                                document.getElementById('handled-list-container').innerHTML = data.handledQueuesHtml;

                                // Update timestamp
                                const now = new Date();
                                const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                                               now.getMinutes().toString().padStart(2, '0') + ':' + 
                                               now.getSeconds().toString().padStart(2, '0');
                                timeElement.innerText = timeStr;
                            })
                            .catch(error => console.error("Update failed:", error));
                    }

                    function renderWaitingList(queues) {
                        const listContent = document.getElementById('waiting-list-content');
                        if (!listContent) return;

                        if (queues.length === 0) {
                            listContent.innerHTML = `
                                <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                                    <p class="text-gray-400 italic">Semua antrian sudah dipanggil :)</p>
                                </div>
                            `;
                            return;
                        }

                        let html = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">';
                        queues.forEach(q => {
                            html += `
                                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 flex justify-between items-center shadow-sm">
                                    <div>
                                        <div class="text-2xl font-black text-gray-800 dark:text-white">${q.nomor_antrian}</div>
                                        <div class="text-xs text-gray-400 font-medium">${q.user ? q.user.name : 'Pasien'}</div>
                                    </div>
                                    <div class="px-2 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] uppercase font-bold rounded">
                                        ${q.service ? q.service.kode_prefix : '-'}
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        listContent.innerHTML = html;
                    }

                    function updateWaitingCount(total) {
                        const countEl = document.querySelector('#waiting-list-container span.bg-gray-200');
                        if (countEl) {
                            countEl.innerText = `${total} Orang`;
                        }
                    }

                    function updatePagination(html) {
                        const pagContainer = document.getElementById('waiting-pagination-container');
                        if (pagContainer) {
                            pagContainer.innerHTML = html || '';
                        }
                    }

                    function showAlert(type, message) {
                        const el = type === 'status' ? alertStatus : alertError;
                        const other = type === 'status' ? alertError : alertStatus;
                        
                        other.classList.add('hidden');
                        el.innerText = message;
                        el.classList.remove('hidden');
                        
                        // Hide after 5 seconds
                        setTimeout(() => {
                            el.classList.add('hidden');
                        }, 5000);
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
                            
                            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=' + page;
                            window.history.pushState({path:newUrl},'',newUrl);
                            
                            fetchDashboardData();
                        }
                    });

                    // Intercept form submissions (Panggil, Mulai, Selesai, Skip, Recall)
                    document.addEventListener('submit', function(e) {
                        const form = e.target.closest('#petugas-dashboard-container form');
                        if (form) {
                            e.preventDefault();
                            
                            const formData = new FormData(form);
                            const action = form.getAttribute('action');
                            const method = formData.get('_method') || form.getAttribute('method') || 'POST';

                            fetch(action, {
                                method: method,
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status) {
                                    showAlert('status', data.status);
                                    fetchDashboardData(); // Update UI immediately
                                } else if (data.error) {
                                    showAlert('error', data.error);
                                }
                            })
                            .catch(error => {
                                console.error("Action failed:", error);
                                showAlert('error', "Terjadi kesalahan sistem.");
                            });
                        }
                    });
                });
            </script>
        </div>
    </div>
</x-app-layout>
