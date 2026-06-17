<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Plan;
use App\Services\PlanLimitService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PlanController extends Controller
{
    public function __construct(
        private PlanLimitService $planLimitService
    ){}

    public function index(): View|Factory|Application
    {
        $plans = Plan::with(['limits', 'features'])
            ->where('status', 1)
            ->where('is_visible', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(function (Plan $plan) {
                return [
                    'title' => $plan->name,
                    'yearly' => number_format((float)$plan->price, 0),
                    'sites' => $plan->coverage,
                    'desc' => $plan->description,
                    'features' => array_merge(
                        $plan->limits->sortBy('sort_order')->map(fn($limit) => $limit->label . ' - ' . $limit->value)->values()->all(),
                        $plan->features->sortBy('sort_order')->pluck('feature')->values()->all()
                    ),
                ];
            });

        $activePlan = $this->planLimitService->getActivePlan();

        return view('admin-views.pricing', compact('plans', 'activePlan'));
    }
}
