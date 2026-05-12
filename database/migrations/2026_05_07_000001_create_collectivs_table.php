<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collectivs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 16);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('ao');
            $table->string('jenis_permohonan_kredit');
            $table->string('ktp_file');
            $table->enum('status', ['belum_diverifikasi', 'terverifikasi', 'expired'])->default('belum_diverifikasi');
            $table->timestamps();

            $table->index('nik');
            $table->index('ao');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collectivs');
    }
};
