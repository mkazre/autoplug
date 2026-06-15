<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plan_applications', function (Blueprint $table) {
            $table->string('admin_signature_path')->nullable()->after('approved_by');
            $table->string('approved_ip')->nullable()->after('admin_signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('plan_applications', function (Blueprint $table) {
            $table->dropColumn(['admin_signature_path', 'approved_ip']);
        });
    }
};
