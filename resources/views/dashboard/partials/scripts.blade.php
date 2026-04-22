<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElement = document.getElementById('refresh-countdown');
        const statusBadge = document.getElementById('status-badge');
        const topAccent = document.getElementById('top-accent-line');
        const queueNumberText = document.getElementById('queue-number-text');
        const dynamicContent = document.getElementById('dynamic-status-content');
        
        let timeLeft = 10;

        function fetchQueueStatus() {
            fetch('{{ route('dashboard.api.status') }}')
                .then(response => response.json())
                .then(data => {
                    // Reset countdown visually
                    timeLeft = 10;
                    if (countdownElement) countdownElement.textContent = timeLeft;

                    if (!data.activeQueue) {
                        // If queue is done or deleted, reload to show Condition C or A
                        window.location.reload();
                        return;
                    }

                    const q = data.activeQueue;
                    
                    // 1. Update Number
                    if (queueNumberText) queueNumberText.innerText = q.nomor_antrian;

                    // 2. Update Badge & Accent
                    if (statusBadge) {
                        updateBadge(statusBadge, q);
                    }
                    if (topAccent) {
                        updateAccent(topAccent, q);
                    }

                    // 3. Update Dynamic Content
                    updateDynamicContent(dynamicContent, q, data.position, data.estimasi);

                    // 4. Update History Table
                    if (data.historyQueues) {
                        updateHistoryTable(data.historyQueues);
                    }
                })
                .catch(error => console.error("Update failed:", error));
        }

        function updateHistoryTable(history) {
            const tbody = document.getElementById('history-table-body');
            if (!tbody) return;

            if (history.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm italic">
                            Belum ada riwayat kunjungan sebelumnya.
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            history.forEach(item => {
                const date = item.schedule ? formatDate(item.schedule.tanggal) : formatDate(item.created_at);
                const statusHtml = getStatusBadge(item);
                
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            ${date}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            ${item.service ? item.service.nama_layanan : '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400">
                            ${item.nomor_antrian}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            ${statusHtml}
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function formatDate(dateStr) {
            const d = new Date(dateStr);
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            // Simple formatting for now
            return d.toLocaleDateString('id-ID', options);
        }

        function getStatusBadge(item) {
            return `<span class="px-2 py-1 ${item.status_color} rounded text-xs font-bold border">${item.status_label}</span>`;
        }

        function updateBadge(el, q) {
            el.className = `px-4 py-2 rounded-full border font-bold text-sm tracking-widest ${q.status_color}`;
            el.innerText = q.status_label;
        }

        function updateAccent(el, q) {
            el.className = `h-2 w-full ${q.accent_color}`;
        }

        function updateDynamicContent(el, q, position, estimasi) {
            if (q.status === 'waiting') {
                el.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 text-center">
                        <div>
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-1">Prakiraan Posisi</p>
                            <p class="text-3xl font-black text-yellow-800 dark:text-yellow-300">Ke-${position}</p>
                        </div>
                        <div class="hidden md:block w-px bg-yellow-200 dark:bg-yellow-800/50 mx-auto"></div>
                        <div class="block md:hidden h-px bg-yellow-200 dark:bg-yellow-800/50 w-full my-2"></div>
                        <div>
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-1">Estimasi Tunggu</p>
                            <p class="text-3xl font-black text-yellow-800 dark:text-yellow-300">± ${estimasi} <span class="text-lg font-bold">menit</span></p>
                        </div>
                    </div>
                `;
            } else if (q.status === 'called') {
                el.innerHTML = `
                    <div class="p-8 rounded-2xl bg-blue-600 text-white text-center shadow-xl animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        <h3 class="text-3xl font-black uppercase tracking-wider mb-2">GILIRAN ANDA!</h3>
                        <p class="text-blue-100 text-lg mb-6">Silakan segera menuju ke:</p>
                        <div class="inline-block px-8 py-3 bg-white text-blue-800 rounded-full text-2xl font-black tracking-widest shadow-inner">
                            ${q.counter ? q.counter.name : 'LOKET'}
                        </div>
                    </div>
                `;
            } else if (q.status === 'processing') {
                el.innerHTML = `
                    <div class="p-8 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 text-center">
                        <h3 class="text-2xl font-bold text-indigo-800 dark:text-indigo-300 mb-2">Anda Sedang Dilayani</h3>
                        <p class="text-indigo-600 dark:text-indigo-400">Petugas di ${q.counter ? q.counter.name : 'Loket'} sedang memproses keperluan Anda.</p>
                    </div>
                `;
            } else if (q.status === 'skipped') {
                el.innerHTML = `
                    <div class="p-8 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="text-xl font-bold text-red-800 dark:text-red-400 mb-2">Nomor Anda Dilewati</h3>
                        <p class="text-red-600 dark:text-red-300">Panggilan untuk nomor Anda tidak direspons. Silakan melapor kepada petugas untuk melakukan pemanggilan ulang.</p>
                    </div>
                `;
            }
        }

        // Polling interval
        setInterval(function() {
            timeLeft--;
            if (countdownElement) countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                fetchQueueStatus();
            }
        }, 1000);
    });
</script>
