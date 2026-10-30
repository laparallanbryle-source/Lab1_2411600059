<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Company name
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('payment_terms')->nullable(); // e.g., Net 30, COD
            $table->integer('lead_time_days')->default(7);
            $table->decimal('minimum_order_amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes(); // Required for Lab 7
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};