<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('trip_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trip_id');
            $table->unsignedSmallInteger('product_id')->nullable(false)->change();
        });
    }
};
