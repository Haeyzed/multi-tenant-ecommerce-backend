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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Multi-Tenant E-Commerce');
            $table->string('support_email')->default('support@example.com');
            $table->string('currency')->default('USD');
            $table->boolean('maintenance_mode')->default(false);
            $table->integer('trial_days')->default(14);
            $table->foreignId('default_plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
