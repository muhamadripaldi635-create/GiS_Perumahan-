<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('risikos')) {
            return;
        }

        Schema::create('risikos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perumahan_id')->constrained('perumahans')->cascadeOnDelete();
            $table->string('nama_risiko', 180);
            $table->string('tipe', 120)->nullable();
            $table->enum('tingkat', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('rendah');
            $table->text('deskripsi')->nullable();
            $table->text('mitigasi')->nullable();
            $table->string('status_tindak_lanjut', 120)->nullable();
            $table->timestamps();
            $table->index(['perumahan_id', 'tingkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risikos');
    }
};
