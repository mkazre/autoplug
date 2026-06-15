<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plan_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('service'); // service | maintenance
            $table->text('description')->nullable();
            $table->unsignedInteger('min_km_excl')->default(0);
            $table->unsignedInteger('max_km')->nullable();
            $table->unsignedInteger('max_age_years')->nullable();
            $table->boolean('requires_full_history')->default(false);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->string('terms_version')->default('v1');
            $table->string('terms_doc_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('plan_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_product_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_category');
            $table->unsignedSmallInteger('term_months')->default(12);
            $table->unsignedSmallInteger('installment_count')->default(1);
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('upfront_price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('plan_benefit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_product_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->decimal('coverage_limit', 10, 2)->nullable();
            $table->string('coverage_unit')->default('per_year'); // per_item | per_year | lifetime
            $table->timestamps();
        });

        Schema::create('plan_holder_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_product_id')->constrained()->cascadeOnDelete();
            $table->string('discount_type')->default('percentage'); // percentage | fixed
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->string('applies_to')->default('labour_only'); // quote_total | labour_only | parts_only
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('plan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pricing_tier_id')->nullable()->constrained('plan_pricing_tiers')->nullOnDelete();
            $table->string('status')->default('draft'); // draft|submitted|under_review|approved|rejected|active|expired|cancelled
            $table->string('terms_version')->nullable();
            $table->string('payment_method')->nullable(); // upfront | installments
            $table->string('referral_code')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('plan_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_application_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_path');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_application_id')->constrained()->cascadeOnDelete();
            $table->string('signatory_name');
            $table->string('signature_path');
            $table->timestamp('signed_at');
            $table->string('ip_address')->nullable();
            $table->string('device_info', 512)->nullable();
            $table->string('terms_version')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pricing_tier_id')->nullable()->constrained('plan_pricing_tiers')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active|lapsed|suspended|cancelled|completed
            $table->unsignedInteger('km_at_start')->nullable();
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->unsignedSmallInteger('missed_count')->default(0);
            $table->string('contract_path')->nullable();
            $table->string('referral_code')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_subscription_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('plan_installment_id')->nullable();
            $table->string('gateway')->default('payfast');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('reference')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_subscription_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_number');
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->date('due_date');
            $table->string('status')->default('pending'); // pending|paid|overdue|failed
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('plan_payment_id')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('redemption_type')->default('service'); // service | part_replacement
            $table->text('description')->nullable();
            $table->json('items_json')->nullable();
            $table->decimal('amount_claimed', 10, 2)->default(0);
            $table->decimal('amount_approved', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending|approved|rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('garage_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referring_garage_id')->constrained('garages')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('plan_subscription_id')->nullable()->constrained('plan_subscriptions')->nullOnDelete();
            $table->string('reward_status')->default('pending'); // pending|approved|paid
            $table->decimal('reward_amount', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('garage_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garage_id')->constrained('garages')->cascadeOnDelete();
            $table->string('type'); // redemption | referral
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending | paid
            $table->string('cycle')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->json('changes_json')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
        });

        Schema::table('garages', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->unique()->after('verified');
        });
    }

    public function down(): void
    {
        Schema::table('garages', fn (Blueprint $t) => $t->dropColumn('referral_code'));
        foreach ([
            'plan_audit_logs', 'garage_payouts', 'garage_referrals', 'plan_redemptions',
            'plan_installments', 'plan_payments', 'plan_subscriptions', 'plan_signatures',
            'plan_application_documents', 'plan_applications', 'plan_holder_discounts',
            'plan_benefit_items', 'plan_pricing_tiers', 'plan_products',
        ] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
