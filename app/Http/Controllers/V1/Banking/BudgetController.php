<?php

namespace App\Http\Controllers\V1\Banking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Budget;
use App\Services\Banking\BudgetService;

class BudgetController extends Controller
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function view(Request $request) {
    
        // $userId = auth()->id(); 
        $userId = 2;
        $month = $request->input('month', date('m')); // 01–12
        $year  = $request->input('year', date('Y'));
        $selectedMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT); // e.g. "2025-11"
        // budgets for the selected month
        $budgetData = $this->budgetService->getBudgetDetails($userId, $selectedMonth);

        // If it's an AJAX call, return only the partial view
        if ($request->ajax()) {
            return view('partials.budget-content', compact('budgetData', 'month'))->render();
        }

        // Otherwise, return the full page view
        return view('layouts.app.budgets', compact('budgetData', 'month'));
    }


    public function getUserBudgets(Request $request)
    {
        // dd($request);
        try {
            $budgets = $this->budgetService->getBudgetDetails($request->id);
            if ($budgets['status'] === true) {
                return response()->json(['status' => true, 'data' => $budgets['data']]);
            } else {
                return response()->json(['status' => false, 'message' => 'No budgets found']);
            }
        } catch (\Throwable $th) {
            \Log::error('BudgetController->index ' . $th->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to retrieve budgets']);
        }
    }

    public function store(Request $request)
    {
        try {
            $budget = $this->budgetService->createBudget($request->user()->id, $request->all());
            if ($budget['status' === true]) {
                return response()->json(['status' => true, 'message' => $budget['message'], 'data' => $budget['data']]);
            } else {
                return response()->json(['status' => false, 'message' => $budget['message']]);
            }
        } catch (\Exception $e) {
            \Log::error('BudgetController->store ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to create budget']);
        }
    }

    public function show($id)
    {
        $budget = Budget::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return response()->json([
            'budget' => $budget,
            'spent' => $budget->totalSpent(),
            'remaining' => $budget->remaining(),
        ]);
    }
}
