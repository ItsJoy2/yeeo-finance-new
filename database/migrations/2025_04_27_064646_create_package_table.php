<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('plan_name');
            $table->string('image')->nullable();
            $table->decimal('min_investment', 10, 2);
            $table->decimal('max_investment', 10, 2);
            $table->enum('return_type', ['daily', 'monthly']);
            $table->integer('duration')->default(0);
            $table->decimal('pnl_return', 5, 2);
            $table->decimal('pnl_bonus', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package');
    }
};
