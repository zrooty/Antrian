<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rekapitulasi Antrian (Admin)') }}
        </h2>
    </x-slot>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    
    <style>
        /* Premium DataTables Styling */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.25rem 2rem 0.25rem 0.5rem;
        }
        table.dataTable {
            border-collapse: collapse !important;
            border-radius: 0.75rem;
            overflow: hidden;
            border: none;
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }
        table.dataTable thead th {
            background-color: rgba(248, 250, 252, 0.05); /* Soft background for header */
            color: #94a3b8; /* Muted light color for header in dark mode */
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.1) !important;
        }
        table.dataTable tbody td {
            padding: 1rem !important;
            border-bottom: 1px solid rgba(241, 245, 249, 0.05) !important;
            color: inherit; /* Follow container color (white in dark mode) */
        }
        .dataTables_wrapper .dataTables_info, 
        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            color: inherit !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #3b82f6 !important;
            color: white !important;
            border: none !important;
            border-radius: 0.5rem !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-6">
                        <h3 class="text-2xl font-black text-gray-800 dark:text-white">Laporan Registrasi Antrian</h3>
                        <p class="text-gray-500 dark:text-gray-400">Memantau seluruh aktivitas pendaftaran pasien secara real-time.</p>
                    </div>

                    <style>
                        /* Menghilangkan paksaan warna putih pada kontrol DataTables */
                        .dataTables_length select, .dataTables_filter input {
                            background-color: transparent !important;
                            color: inherit !important;
                            border: 1px solid rgba(255, 255, 255, 0.1) !important;
                        }
                        .dataTables_wrapper .dataTables_paginate .paginate_button {
                            color: inherit !important;
                        }
                    </style>

                    <div class="overflow-x-auto">
                        <table id="rekapTable" class="display responsive nowrap w-full">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Antrian</th>
                                    <th>Nama Pasien</th>
                                    <th>Layanan</th>
                                    <th>Keluhan</th>
                                    <th>Status</th>
                                    <th>Jam Daftar</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#rekapTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.rekap.data') }}",
                columns: [
                    { data: 'tanggal', name: 'schedules.tanggal' },
                    { data: 'nomor_antrian', name: 'queues.nomor_antrian' },
                    { data: 'nama_pasien', name: 'users.name' },
                    { data: 'nama_layanan', name: 'services.nama_layanan' },
                    { data: 'keluhan', name: 'queues.keluhan' },
                    { data: 'status', name: 'queues.status' },
                    { data: 'created_at', name: 'queues.created_at' },
                ],
                order: [[0, 'desc'], [6, 'desc']], // Urutkan berdasarkan tanggal dan jam terbaru
                language: {
                    search: "Cari Data:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ antrian",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Lanjut",
                        previous: "Kembali"
                    }
                }
            });
        });
    </script>
</x-app-layout>
