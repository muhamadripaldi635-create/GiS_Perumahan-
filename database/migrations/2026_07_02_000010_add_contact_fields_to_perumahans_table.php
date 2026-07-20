<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('perumahans')) {
            return;
        }

        Schema::table('perumahans', function (Blueprint $table) {
            if (! Schema::hasColumn('perumahans', 'telepon')) {
                $table->string('telepon', 50)->nullable()->after('jenis_perumahan');
            }
            if (! Schema::hasColumn('perumahans', 'email')) {
                $table->string('email', 160)->nullable()->after('telepon');
            }
            if (! Schema::hasColumn('perumahans', 'website_url')) {
                $table->string('website_url', 500)->nullable()->after('email');
            }
            if (! Schema::hasColumn('perumahans', 'google_maps_url')) {
                $table->string('google_maps_url', 800)->nullable()->after('website_url');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('perumahans')) {
            return;
        }

        Schema::table('perumahans', function (Blueprint $table) {
            foreach (['google_maps_url', 'website_url', 'email', 'telepon'] as $column) {
                if (Schema::hasColumn('perumahans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
