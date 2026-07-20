<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('perumahans', function (Blueprint $table) {
            $table->boolean('is_recommended')->default(false)->after('google_maps_url')->index();
        });
    }

    public function down(): void
    {
        Schema::table('perumahans', function (Blueprint $table) {
            $table->dropColumn('is_recommended');
        });
    }
};
