<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Control Panel -->
    <div class="lg:col-span-2 space-y-8">
        <div id="active-queue-container">
            @include('petugas.partials.active-queue')
        </div>
        <div id="waiting-list-container">
            @include('petugas.partials.waiting-list')
        </div>
    </div>

    <!-- Sidebar: Skipped/History -->
    <div class="space-y-8">
        <div id="skipped-list-container">
            @include('petugas.partials.skipped-list')
        </div>
        <div id="handled-list-container">
            @include('petugas.partials.handled-list')
        </div>
    </div>
</div>
