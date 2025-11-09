<?php

namespace App\Services\Banking;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\Budget;

class BudgetService
{
    public function getBudgetDetails(int $userId)
    {
        try {
            $budgets = Budget::where('user_id', $userId)
                ->with('category')
                ->get()
                ->map(function ($budget) {
                    return [
                        'id' => $budget->id,
                        'category' => $budget->category->name,
                        'budget_limit' => $budget->amount,
                        'spent' => $budget->totalSpent(),
                        'remaining' => $budget->remaining(),
                        'progress' => round($budget->spendingPercentage(), 2),
                        'status' => $budget->getStatus(),
                        'message' => $budget->getBudgetMessage(),
                    ];
                });

            return ([
                'status' => true,
                'message' => 'Budgets with spending details',
                'data' => $budgets
            ]);
        } catch (\Exception $e) {
            \Log::error('BudgetService->getBudgetDetails ' . $e->getMessage());
            return (['status' => false, 'message' => 'service issue']);
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
