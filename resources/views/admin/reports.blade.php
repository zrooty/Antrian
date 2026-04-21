<x-admin-layout>
    <x-slot name="header">
        {{ __('Laporan & Analisis') }}
    </x-slot>

    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm mb-6">
        <form id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <x-input-label for="start_date" value="Tanggal Mulai" />
                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" />
            </div>
            <div>
                <x-input-label for="end_date" value="Tanggal Selesai" />
                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" />
            </div>
            <div>
                <x-primary-button type="submit" id="filter-btn">Filter Laporan</x-primary-button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
        <div class="p-8">
            <div class="overflow-x-auto">
                <table id="reportTable" class="display responsive nowrap w-full text-sm">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>No. Antrian</th>
                            <th>Nama Pasien</th>
                            <th>Layanan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#reportTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('admin.reports.data') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'created_at', name: 'queues.created_at' },
                    { data: 'nomor_antrian', name: 'queues.nomor_antrian' },
                    { data: 'nama_pasien', name: 'users.name' },
                    { data: 'nama_layanan', name: 'services.nama_layanan' },
                    { data: 'status', name: 'queues.status' },
                ],
                order: [[0, 'desc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                }
            });

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });
        });
    </script>
</x-admin-layout>
