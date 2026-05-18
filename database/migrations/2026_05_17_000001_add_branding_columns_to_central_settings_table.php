<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('site_logo_url')->nullable()->after('site_name');
            $table->string('primary_color')->nullable()->after('site_logo_url');
            $table->string('accent_color')->nullable()->after('primary_color');
            $table->string('secondary_color')->nullable()->after('accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_logo_url',
                'primary_color',
                'accent_color',
                'secondary_color',
            ]);
        });
    }
};
