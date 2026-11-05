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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('delivery_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('batch_number', 50)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'batching', 'preparing', 'out_for_delivery', 'delivered', 'cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
