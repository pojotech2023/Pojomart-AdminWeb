<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('coverage')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('plan_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->unsignedInteger('value');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('feature');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        $createPlan = function (array $planData, array $limits, array $features) use ($now): int {
            $planId = DB::table('plans')->insertGetId(array_merge($planData, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));

            foreach ($limits as $index => $limit) {
                DB::table('plan_limits')->insert([
                    'plan_id' => $planId,
                    'key' => $limit['key'],
                    'label' => $limit['label'],
                    'value' => $limit['value'],
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($features as $index => $feature) {
                DB::table('plan_features')->insert([
                    'plan_id' => $planId,
                    'feature' => $feature,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return $planId;
        };

        $currentPlanId = $createPlan(
            [
                'name' => 'Current Active Plan',
                'price' => 0,
                'coverage' => 'Current configuration',
                'description' => 'Hidden internal plan to preserve current live limits.',
                'status' => 1,
                'is_visible' => 0,
                'sort_order' => 0,
            ],
            [
                ['key' => 'product_limit', 'label' => 'Product', 'value' => 4],
                ['key' => 'category_limit', 'label' => 'Category', 'value' => 9],
                ['key' => 'sub_category_limit', 'label' => 'Sub Category', 'value' => 8],
                ['key' => 'banner_limit', 'label' => 'Banner', 'value' => 3],
            ],
            [
                'Website',
                'Admin Panel',
            ]
        );

        $createPlan(
            [
                'name' => 'Starter 1',
                'price' => 4999,
                'coverage' => 'Website + Admin Panel',
                'description' => 'Starter package for a single-store setup.',
                'status' => 1,
                'is_visible' => 1,
                'sort_order' => 1,
            ],
            [
                ['key' => 'product_limit', 'label' => 'Product', 'value' => 300],
                ['key' => 'category_limit', 'label' => 'Category', 'value' => 100],
                ['key' => 'sub_category_limit', 'label' => 'Sub Category', 'value' => 100],
                ['key' => 'banner_limit', 'label' => 'Banner', 'value' => 1],
            ],
            [
                'OTP Integration*',
                'Favorite',
                'Popular Category',
                'Daily Needs',
                'Featured Items',
                'Search Products',
                'Product Details',
                'Image Variant',
                'Coupon Code',
                'Single Branch',
                'Delivery Address',
                'COD',
                'Payment Gateway* - Razorpay',
                'Website',
                'Admin Panel',
            ]
        );

        $createPlan(
            [
                'name' => 'Starter 2',
                'price' => 7999,
                'coverage' => 'Website + Admin Panel',
                'description' => 'Adds more catalog capacity and customer engagement features.',
                'status' => 1,
                'is_visible' => 1,
                'sort_order' => 2,
            ],
            [
                ['key' => 'product_limit', 'label' => 'Product', 'value' => 800],
                ['key' => 'category_limit', 'label' => 'Category', 'value' => 300],
                ['key' => 'sub_category_limit', 'label' => 'Sub Category', 'value' => 300],
                ['key' => 'banner_limit', 'label' => 'Banner', 'value' => 3],
            ],
            [
                'OTP Integration*',
                'Favorite',
                'Dark Mode',
                'Popular Category',
                'Daily Needs',
                'Featured Items',
                'Search Products',
                'Product Details',
                'Image Variant',
                'Coupon Code',
                'Single Branch',
                'Delivery Address',
                'COD',
                'Payment Gateway* - Razorpay, Paypal',
                'Customer Chat',
                'WhatsApp Icon',
                'Website',
                'Admin Panel',
            ]
        );

        $createPlan(
            [
                'name' => 'Starter 3',
                'price' => 9999,
                'coverage' => 'Website + Admin Panel',
                'description' => 'Adds POS and reporting tools for growing operations.',
                'status' => 1,
                'is_visible' => 1,
                'sort_order' => 3,
            ],
            [
                ['key' => 'product_limit', 'label' => 'Product', 'value' => 1000],
                ['key' => 'category_limit', 'label' => 'Category', 'value' => 500],
                ['key' => 'sub_category_limit', 'label' => 'Sub Category', 'value' => 500],
                ['key' => 'banner_limit', 'label' => 'Banner', 'value' => 5],
            ],
            [
                'OTP Integration*',
                'Favorite',
                'Dark Mode',
                'Popular Category',
                'Daily Needs',
                'Featured Items',
                'Search Products',
                'Product Details',
                'Image Variant',
                'Coupon Code',
                'Single Branch',
                'Delivery Address',
                'COD',
                'Payment Gateway* - Razorpay',
                'Customer Chat',
                'WhatsApp Icon',
                'POS System',
                'Flash Sale',
                'Sales & Earning Report',
                'Analytics',
                'Employee Login',
                'Website',
                'Admin Panel',
            ]
        );

        $createPlan(
            [
                'name' => 'Starter 4',
                'price' => 15999,
                'coverage' => 'Website + Admin Panel + Customer Mobile App',
                'description' => 'Adds Android customer app and broader payment support.',
                'status' => 1,
                'is_visible' => 1,
                'sort_order' => 4,
            ],
            [
                ['key' => 'product_limit', 'label' => 'Product', 'value' => 1500],
                ['key' => 'category_limit', 'label' => 'Category', 'value' => 700],
                ['key' => 'sub_category_limit', 'label' => 'Sub Category', 'value' => 700],
                ['key' => 'banner_limit', 'label' => 'Banner', 'value' => 10],
            ],
            [
                'OTP Integration*',
                'Favorite',
                'Dark Mode',
                'Popular Category',
                'Daily Needs',
                'Featured Items',
                'Search Products',
                'Product Details',
                'Image Variant',
                'Coupon Code',
                'Single Branch',
                'Delivery Address',
                'COD',
                'Payment Gateway* - Razorpay, Stripe, Paypal',
                'Customer Chat',
                'Admin Panel',
                'WhatsApp Icon',
                'POS System',
                'Flash Sale',
                'Sales & Earning Report',
                'Analytics',
                'Push Notification',
                'Employee Login',
                'Customer Mobile App - Android',
                'Website',
            ]
        );

        $createPlan(
            [
                'name' => 'Starter 5',
                'price' => 19999,
                'coverage' => 'Website + Admin Panel + Customer App + Deliveryman App',
                'description' => 'Adds delivery workflow and multi-branch operation.',
                'status' => 1,
                'is_visible' => 1,
                'sort_order' => 5,
            ],
            [
                ['key' => 'product_limit', 'label' => 'Product', 'value' => 1500],
                ['key' => 'category_limit', 'label' => 'Category', 'value' => 700],
                ['key' => 'sub_category_limit', 'label' => 'Sub Category', 'value' => 700],
                ['key' => 'banner_limit', 'label' => 'Banner', 'value' => 10],
            ],
            [
                'OTP Integration*',
                'Favorite',
                'Dark Mode',
                'Popular Category',
                'Daily Needs',
                'Featured Items',
                'Search Products',
                'Product Details',
                'Image Variant - 6',
                'Coupon Code',
                'Single Branch',
                'Delivery Address',
                'COD',
                'Payment Gateway* - Razorpay, Stripe, Paypal',
                'Customer Chat',
                'Admin Panel',
                'WhatsApp Icon',
                'POS System',
                'Flash Sale',
                'Sales & Earning Report',
                'Analytics',
                'Employee Login',
                'Push Notification',
                'Multi Branch Setup',
                'Branch Login',
                'Customer Mobile App - Android',
                'Deliveryman App - Android',
                'Website',
            ]
        );

        $createPlan(
            [
                'name' => 'Starter 6',
                'price' => 29999,
                'coverage' => 'Website + Admin Panel + Customer App + Deliveryman App',
                'description' => 'Adds iOS customer app and full reporting stack.',
                'status' => 1,
                'is_visible' => 1,
                'sort_order' => 6,
            ],
            [
                ['key' => 'product_limit', 'label' => 'Product', 'value' => 2000],
                ['key' => 'category_limit', 'label' => 'Category', 'value' => 1000],
                ['key' => 'sub_category_limit', 'label' => 'Sub Category', 'value' => 1000],
                ['key' => 'banner_limit', 'label' => 'Banner', 'value' => 10],
            ],
            [
                'OTP Integration*',
                'Favorite',
                'Dark Mode',
                'Popular Category',
                'Daily Needs',
                'Featured Items',
                'Search Products',
                'Product Details',
                'Image Variant - 6',
                'Coupon Code',
                'Single Branch',
                'Delivery Address',
                'COD',
                'Payment Gateway* - Razorpay, Stripe, Paypal',
                'Customer Chat',
                'Admin Panel',
                'WhatsApp Icon',
                'POS System',
                'Flash Sale',
                'Sales Report',
                'Order Report',
                'Earning Report',
                'Expense Report',
                'Analytics',
                'Employee Login',
                'Push Notification',
                'Multi Branch Setup',
                'Branch Login',
                'Customer Mobile App - Android & iOS',
                'Deliveryman App - Android',
                'Website',
            ]
        );

        DB::table('business_settings')->updateOrInsert(
            ['key' => 'active_plan_id'],
            ['value' => (string)$currentPlanId, 'updated_at' => $now, 'created_at' => $now]
        );
    }

    public function down(): void
    {
        DB::table('business_settings')->where('key', 'active_plan_id')->delete();
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('plan_limits');
        Schema::dropIfExists('plans');
    }
};
