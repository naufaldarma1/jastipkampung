<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120);
            $table->string('origin', 80);
            $table->string('destination', 80)->default('Sekaran, UNNES');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('quota');
            $table->string('unit', 30)->default('paket');
            $table->unsignedInteger('price');
            $table->unsignedInteger('service_fee')->default(0);
            $table->dateTime('order_deadline');
            $table->dateTime('return_at');
            $table->string('pickup_point', 150);
            $table->string('status')->default('open');
            $table->timestamps();
            $table->index(['status', 'order_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
