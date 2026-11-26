<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('faculty', 100)->nullable()->after('role');
            $table->string('origin_city', 80)->nullable()->after('faculty');
            $table->text('store_bio')->nullable()->after('origin_city');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jastiper_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jastiper_slug', 100)->nullable()->index();
            $table->foreignId('trip_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('product_id')->nullable()->index();
            $table->string('product_title');
            $table->unsignedTinyInteger('product_rating');
            $table->unsignedTinyInteger('store_rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['faculty', 'origin_city', 'store_bio']);
        });
    }
};
