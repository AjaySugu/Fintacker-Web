<?php

namespace App\Services\Banking;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\Budget;
use Carbon\Carbon;

class BudgetService
{
    // public function getBudgetDetails(int $userId, ?string $selectedMonth = null)
    // {
    //     try {
    //         $selectedMonth = $selectedMonth ?? now()->format('Y-m');
    //         $monthStart = Carbon::parse($selectedMonth)->startOfMonth();
    //         $monthEnd   = Carbon::parse($selectedMonth)->endOfMonth();

    //         $today = Carbon::now();
    //         $endOfMonth = Carbon::now()->endOfMonth();
    //         $daysLeft = $today->diffInDays($endOfMonth) + 1;

    //         $budgets = Budget::where('user_id', $userId)
    //         ->whereBetween('created_at', [$monthStart, $monthEnd])
    //         ->with('category')
    //         ->get();

    //         // Calculate totals
    //         $totalBudget = $budgets->sum('amount');
    //         $totalSpent = $budgets->sum(function ($budget) {
    //             return $budget->totalSpent();
    //         });

    //         $totalRemaining = $totalBudget - $totalSpent;
    //         $totalDailyAllowance = $daysLeft > 0 ? round($totalRemaining / $daysLeft, 2) : 0;


    //         // Map each budget to detailed structure
    //         $budgetDetails = $budgets->map(function ($budget) {
    //             return [
    //                 'id' => $budget->id,
    //                 'category' => optional($budget->category)->name ?? 'Uncategorized',
    //                 'category_icon' => optional($budget->category)->icon ?? 'fi fi-rr-wallet',
    //                 'budget_limit' => $budget->amount,
    //                 'spent' => $budget->totalSpent(),
    //                 'remaining' => $budget->remaining(),
    //                 'progress' => round($budget->spendingPercentage(), 2),
    //                 'status' => $budget->getStatus(),
    //                 'message' => $budget->getBudgetMessage(),
    //             ];
    //         });

    //         return [
    //             'status' => true,
    //             'message' => 'Budgets with spending details',
    //             'data' => [
    //                 'budgets' => $budgetDetails,
    //                 'total_budget' => $totalBudget,
    //                 'total_spent' => $totalSpent,
    //                 'total_remaining' => $totalBudget - $totalSpent,
    //                 'overall_message' => $this->getTotalBudgetMessage($totalSpent, $totalBudget, $totalDailyAllowance),
    //             ]
    //         ];
    //     } catch (\Exception $e) {
    //         \Log::error('BudgetService->getBudgetDetails ' . $e->getMessage());
    //         return (['status' => false, 'message' => 'service issue']);
    //     }     
    // }

    public function getBudgetDetails(int $userId, ?string $selectedMonth = null)
{
    try {
        $selectedMonth = $selectedMonth ?? now()->format('Y-m');
        $monthStart = Carbon::parse($selectedMonth)->startOfMonth();
        $monthEnd   = Carbon::parse($selectedMonth)->endOfMonth();

        // Days left in month
        $today = Carbon::now();
        $endOfMonth = Carbon::now()->endOfMonth();
        $daysLeft = $today->diffInDays($endOfMonth) + 1;

        // Fetch budgets
        $budgets = Budget::where('user_id', $userId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('category')
            ->get();

        // Calculate totals
        $totalBudget = $budgets->sum('amount');
        $totalSpent = $budgets->sum(fn($budget) => $budget->totalSpent());
        $totalRemaining = $totalBudget - $totalSpent;

        // Daily allowance for total
        $totalDailyAllowance = $daysLeft > 0 ? round($totalRemaining / $daysLeft, 2) : 0;

        // Map each category
        $budgetDetails = $budgets->map(function ($budget) {
            return [
                'id' => $budget->id,
                'category' => optional($budget->category)->name ?? 'Uncategorized',
                'category_icon' => optional($budget->category)->icon ?? 'fi fi-rr-wallet',
                'category_color' => $budget->category->color ?? '#000',
                'budget_limit' => $budget->amount,
                'spent' => $budget->totalSpent(),
                'remaining' => $budget->remaining(),
                'progress' => round($budget->spendingPercentage(), 2),
                'status' => $budget->getStatus(),
                'message' => $budget->getBudgetMessage(),
            ];
        });

        return [
            'status' => true,
            'message' => 'Budgets with spending details',
            'data' => [
                'budgets' => $budgetDetails,
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalRemaining,
                'total_daily_allowance' => $totalDailyAllowance,
                'overall_message' => Budget::getTotalBudgetMessage(
    $totalSpent,
    $totalBudget,
    $totalDailyAllowance
),
            ]
        ];

    } catch (\Exception $e) {
        \Log::error('BudgetService->getBudgetDetails ' . $e->getMessage());
        return ['status' => false, 'message' => 'service issue'];
    }
}


    public function createBudget(int $userId, array $data)
    {
        try {
            $budget = new Budget();
            $budget->user_id = $userId;
            $budget->category_id = $data['category_id'];
            $budget->amount = $data['amount'];
            $budget->start_date = $data['start_date']->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d');
            $budget->end_date = $data['end_date']->format('Y-m-d') ?? now()->endOfMonth()->format('Y-m-d');
            $budget->is_recurring = $data['is_recurring'] ?? true;
            $budget->save();

            if ($budget) {
                return (['status' => true, 'message' => 'Budget created successfully', 'data' => $budget]);
            } else {
                return (['status' => false, 'message' => 'Failed to create budget']);
            }
        } catch (\Exception $e) {
            \Log::error('BudgetService->createBudget ' . $e->getMessage());
            throw new \Exception('Failed to create budget');
        }
    }
}
