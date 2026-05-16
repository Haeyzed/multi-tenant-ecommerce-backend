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
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // email|sms|push|whatsapp|in_app
            $table->string('label');
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event')->index(); // order.placed, ticket.replied, etc.
            $table->string('channel'); // matches notification_channels.key
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->string('greeting')->nullable();
            $table->string('closing')->nullable();
            $table->string('sign_off')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('logo_alt')->nullable();
            $table->string('header_bg_color')->nullable();
            $table->string('accent_color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['event', 'channel']);
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable'); // user or tenant
            $table->string('event');
            $table->string('channel');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['notifiable_type', 'notifiable_id', 'event', 'channel'], 'np_central_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_channels');
    }
};
