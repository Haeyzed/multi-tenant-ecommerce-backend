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
        Schema::create('third_party_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->unique(); // e.g., 'paystack', 'stripe'
            $table->json('credentials'); // e.g., {'public_key': '...', 'secret_key': '...'}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_party_credentials');
    }
};
