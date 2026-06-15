<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plan_payments', function (Blueprint $table) {
            $table->string('method')->default('card')->after('gateway');
            $table->string('txn_reference')->nullable()->after('reference');
            $table->date('paid_on')->nullable()->after('txn_reference');
            $table->string('proof_path')->nullable()->after('paid_on');
            $table->foreignId('verified_by')->nullable()->after('proof_path')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('plan_payments', function (Blueprint $table) {
            $table->dropColumn(['method', 'txn_reference', 'paid_on', 'proof_path', 'verified_by', 'verified_at']);
        });
    }
};
