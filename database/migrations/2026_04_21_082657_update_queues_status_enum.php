<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->renameColumn('status', 'old_status');
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->enum('status', ['waiting', 'called', 'processing', 'done', 'skipped'])->default('waiting')->after('keluhan');
        });

        // Copy and map data
        \Illuminate\Support\Facades\DB::table('queues')->where('old_status', 'menunggu')->update(['status' => 'waiting']);
        \Illuminate\Support\Facades\DB::table('queues')->where('old_status', 'dipanggil')->update(['status' => 'called']);
        \Illuminate\Support\Facades\DB::table('queues')->where('old_status', 'selesai')->update(['status' => 'done']);
        \Illuminate\Support\Facades\DB::table('queues')->where('old_status', 'batal')->update(['status' => 'skipped']);

        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn('old_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->enum('old_status', ['menunggu', 'dipanggil', 'selesai', 'batal'])->default('menunggu')->after('keluhan');
        });

        \Illuminate\Support\Facades\DB::table('queues')->where('status', 'waiting')->update(['old_status' => 'menunggu']);
        \Illuminate\Support\Facades\DB::table('queues')->where('status', 'called')->update(['old_status' => 'dipanggil']);
        \Illuminate\Support\Facades\DB::table('queues')->where('status', 'done')->update(['old_status' => 'selesai']);
        \Illuminate\Support\Facades\DB::table('queues')->where('status', 'skipped')->update(['old_status' => 'batal']);
        \Illuminate\Support\Facades\DB::table('queues')->where('status', 'processing')->update(['old_status' => 'dipanggil']);

        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->renameColumn('old_status', 'status');
        });
    }
};
