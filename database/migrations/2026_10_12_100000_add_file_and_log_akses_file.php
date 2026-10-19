<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // path file SK hasil upload (relatif terhadap disk "local" = storage/app/private)
        Schema::table('riwayat_akreditasi', function (Blueprint $table) {
            $table->string('file', 255)->nullable()->after('url');
        });

        // log pengunjung publik yang membuka file / link SK
        Schema::create('log_akses_file', function (Blueprint $table) {
            $table->id();
            $table->string('sumber', 40);              // riwayat_akreditasi | riwayat_akreditasi_dokumen
            $table->unsignedBigInteger('id_riwayat');
            $table->unsignedBigInteger('id_prodi')->nullable();
            $table->string('nama_prodi')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('jenis', 10);               // file | link
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('dibuka_pada')->useCurrent();

            $table->index('dibuka_pada');
            $table->index('id_prodi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_akses_file');

        Schema::table('riwayat_akreditasi', function (Blueprint $table) {
            $table->dropColumn('file');
        });
    }
};
