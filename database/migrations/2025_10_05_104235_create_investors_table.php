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
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->decimal('expected_return', 20, 8);
            $table->enum('return_type', ['daily', 'monthly']);
            $table->integer('duration')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_return_date')->nullable();
            $table->integer('received_count')->default(0);
            $table->enum('status', ['running', 'completed', 'cancelled'])->default('running');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
