<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama')->nullable()->after('username');
            $table->string('nip', 30)->nullable()->unique()->after('nama');
            $table->boolean('aktif')->default(true)->after('password');
            $table->string('email')->nullable()->change();
        });

        // email kosong ('') diganti NULL agar tidak bentrok dengan unique index
        DB::table('users')->where('email', '')->update(['email' => null]);
    }

    public function down(): void
    {
        DB::table('users')->whereNull('email')->update(['email' => '']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropColumn(['nama', 'nip', 'aktif']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
