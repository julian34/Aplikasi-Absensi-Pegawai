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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')
                ->constrained('pegawai')
                ->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status_masuk', [
                'tepat_waktu',
                'terlambat'
            ])->nullable();
            $table->enum('status_pulang', [
                'sesuai_jam',
                'pulang_cepat'
            ])->nullable();
            $table->enum('status', [
                'absen_sekali',
                'hadir',
                'terlambat',
                'tidak hadir'
            ])->default('tidak hadir');
            $table->text('keterangan')->nullable();
            $table->unique(['pegawai_id', 'tanggal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
