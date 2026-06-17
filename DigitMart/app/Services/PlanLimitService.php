<?php

namespace App\Services;

use App\Model\BusinessSetting;
use App\Model\Banner;
use App\Model\Category;
use App\Model\Plan;
use App\Model\Product;

class PlanLimitService
{
    private array $defaultLimits = [
        'product_limit' => 4,
        'category_limit' => 9,
        'sub_category_limit' => 8,
        'banner_limit' => 3,
    ];

    private ?Plan $activePlan = null;

    public function getActivePlan(): ?Plan
    {
        if ($this->activePlan) {
            return $this->activePlan;
        }

        $activePlanId = BusinessSetting::where('key', 'active_plan_id')->value('value');

        $planQuery = Plan::with(['limits', 'features'])->where('status', 1);

        if ($activePlanId) {
            $plan = (clone $planQuery)->where('id', (int)$activePlanId)->first();
            if ($plan) {
                return $this->activePlan = $plan;
            }
        }

        return $this->activePlan = (clone $planQuery)->orderBy('sort_order')->first();
    }

    public function getLimit(string $key, int $default): int
    {
        $plan = $this->getActivePlan();

        if (!$plan) {
            return $default;
        }

        $limit = $plan->limits->firstWhere('key', $key);

        return $limit ? (int)$limit->value : $default;
    }

    public function isLimitReached(string $key, int $count, int $default): bool
    {
        return $count >= $this->getLimit($key, $default);
    }

    public function getActivePlanDisplayName(): string
    {
        $plan = $this->getActivePlan();

        if (!$plan) {
            return 'No Plan';
        }

        return $plan->is_visible ? $plan->name : 'Custom Plan';
    }

    public function getTrackedUsage(): array
    {
        return [
            'product_limit' => Product::count(),
            'category_limit' => Category::where('position', 0)->count(),
            'sub_category_limit' => Category::where('position', 1)->count(),
            'banner_limit' => Banner::count(),
        ];
    }

    public function getRemainingCounts(): array
    {
        $usage = $this->getTrackedUsage();

        $remaining = [];
        foreach ($usage as $key => $count) {
            $remaining[$key] = max(0, $this->getLimit($key, $this->defaultLimits[$key]) - $count);
        }

        return $remaining;
    }

    public function getDashboardLimitOverview(): array
    {
        $usage = $this->getTrackedUsage();
        $activeUsage = [
            'product_limit' => Product::active()->count(),
            'category_limit' => Category::active()->where('position', 0)->count(),
            'sub_category_limit' => Category::active()->where('position', 1)->count(),
            'banner_limit' => Banner::active()->count(),
        ];

        return [
            'category_limit' => [
                'label' => 'Category',
                'total_limit' => $this->getLimit('category_limit', $this->defaultLimits['category_limit']),
                'active_limit' => $activeUsage['category_limit'],
                'balance_limit' => max(0, $this->getLimit('category_limit', $this->defaultLimits['category_limit']) - $usage['category_limit']),
            ],
            'sub_category_limit' => [
                'label' => 'Sub Category',
                'total_limit' => $this->getLimit('sub_category_limit', $this->defaultLimits['sub_category_limit']),
                'active_limit' => $activeUsage['sub_category_limit'],
                'balance_limit' => max(0, $this->getLimit('sub_category_limit', $this->defaultLimits['sub_category_limit']) - $usage['sub_category_limit']),
            ],
            'product_limit' => [
                'label' => 'Product',
                'total_limit' => $this->getLimit('product_limit', $this->defaultLimits['product_limit']),
                'active_limit' => $activeUsage['product_limit'],
                'balance_limit' => max(0, $this->getLimit('product_limit', $this->defaultLimits['product_limit']) - $usage['product_limit']),
            ],
            'banner_limit' => [
                'label' => 'Banner',
                'total_limit' => $this->getLimit('banner_limit', $this->defaultLimits['banner_limit']),
                'active_limit' => $activeUsage['banner_limit'],
                'balance_limit' => max(0, $this->getLimit('banner_limit', $this->defaultLimits['banner_limit']) - $usage['banner_limit']),
            ],
        ];
    }

    public function getMinimumRemainingCount(): int
    {
        return min($this->getRemainingCounts());
    }
}
