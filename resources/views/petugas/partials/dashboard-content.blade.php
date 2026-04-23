<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Control Panel -->
    <div class="lg:col-span-2 space-y-8">
        @include('petugas.partials.active-queue')
        @include('petugas.partials.waiting-list')
    </div>

    <!-- Sidebar: Skipped/History -->
    <div class="space-y-8">
        @include('petugas.partials.skipped-list')
        @include('petugas.partials.handled-list')
    </div>
</div>
