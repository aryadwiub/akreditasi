<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proses_akreditasi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_proses');
            $table->integer('nilai')->nullable(); // ditambahkan langsung di database; dicatat di sini agar instalasi baru sama
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proses_akreditasi');
    }
};
