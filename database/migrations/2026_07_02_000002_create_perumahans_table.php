<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('perumahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable()->index();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->decimal('latitude', 10, 7)->nullable()->index();
            $table->decimal('longitude', 10, 7)->nullable()->index();
            $table->unsignedBigInteger('harga_min')->nullable();
            $table->unsignedBigInteger('harga_max')->nullable();
            $table->decimal('luas', 12, 2)->nullable();
            $table->unsignedInteger('jumlah_unit')->nullable();
            $table->string('developer')->nullable();
            $table->enum('status', ['tersedia', 'proses', 'terjual', 'tidak_aktif'])->default('tersedia')->index();
            $table->string('jenis_perumahan')->nullable();
            $table->json('fasilitas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perumahans');
    }
};
