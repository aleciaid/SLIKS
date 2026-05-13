<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('applicants', 'status')) {
            Schema::table('applicants', function (Blueprint $table) {
                $table->enum('status', ['belum_diverifikasi', 'terverifikasi', 'expired'])
                    ->default('belum_diverifikasi')
                    ->after('ktp_file');
            });
        }
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
