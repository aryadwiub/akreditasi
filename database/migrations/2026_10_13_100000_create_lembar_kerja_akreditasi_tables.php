<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // periode (tahun) prodi yang akan diakreditasi
        Schema::create('periode_akreditasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun')->unique();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();
        });

        // prodi yang dicentang pada suatu periode + tahap proses akreditasinya
        Schema::create('periode_akreditasi_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_periode')->constrained('periode_akreditasi')->cascadeOnDelete();
            $table->string('id_prodi', 5);             // prodi.id_prodi (tabel MyISAM, tanpa foreign key)
            $table->unsignedInteger('id_proses')->nullable();
            $table->unsignedBigInteger('diubah_oleh')->nullable();
            $table->timestamps();

            $table->unique(['id_periode', 'id_prodi']);
            $table->index('id_prodi');
            $table->foreign('id_proses')->references('id')->on('proses_akreditasi')->nullOnDelete();
        });

        // log transaksi lembar kerja (tambah periode, ubah proses)
        Schema::create('log_lembar_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('aksi', 30);                // tambah_periode | ubah_proses
            $table->unsignedBigInteger('id_periode')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->string('id_prodi', 5)->nullable();
            $table->string('nama_prodi')->nullable();
            $table->string('proses_lama')->nullable();
            $table->string('proses_baru')->nullable();
            $table->string('keterangan', 500)->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('nama_user')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();

            $table->index('dibuat_pada');
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_lembar_kerja');
        Schema::dropIfExists('periode_akreditasi_prodi');
        Schema::dropIfExists('periode_akreditasi');
    }
};
