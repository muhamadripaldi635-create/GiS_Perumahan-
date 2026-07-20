<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('risiko_perumahans')) {
            return;
        }

        Schema::create('risiko_perumahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perumahan_id')->constrained('perumahans')->cascadeOnDelete();
            $table->string('tipe', 120)->index();
            $table->enum('tingkat', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang')->index();
            $table->text('deskripsi')->nullable();
            $table->text('mitigasi')->nullable();
            $table->enum('status_tindak_lanjut', ['belum_ditinjau', 'dipantau', 'ditindaklanjuti', 'selesai'])->default('belum_ditinjau')->index();
            $table->timestamps();

            $table->index(['perumahan_id', 'tingkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risiko_perumahans');
    }
};
