<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('plan_subscription_id')->nullable()->after('quote_id')->constrained('plan_subscriptions')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('status');
            $table->decimal('net_amount', 10, 2)->nullable()->after('discount_amount');
        });

        Schema::create('plan_discount_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('quote_id')->nullable();
            $table->foreignId('garage_id')->nullable()->constrained('garages')->nullOnDelete();
            $table->decimal('original_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_discount_usages');
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_subscription_id');
            $table->dropColumn(['discount_amount', 'net_amount']);
        });
    }
};
