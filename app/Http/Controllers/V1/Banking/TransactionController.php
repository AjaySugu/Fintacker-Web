<?php

namespace App\Http\Controllers\V1\Banking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Banking\TransactionService;

class TransactionController extends Controller
{

    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    // public function store(Request $request)
    // {
    //     try {
    //         $storedData = $this->transactionService->createTransaction($request->all(), $request->user()->id);
    //         if ($storedData['status'] === true) {
    //             return response()->json(['status' => true, 'message' => $storedData['message']]);
    //         } else {
    //             return response()->json(['status' => false, 'message' => 'Failed to create transaction']);
    //         }
    //     } catch (\Exception $e) {
    //         \Log::error('TransactionController->store ' . $e->getMessage());
    //         return response()->json(['status' => false, 'message' => 'An error occurred while creating the transaction']);
    //     }
    // }

     // 📌 List all transactions for a user
    public function index($userId)
    {
        $transactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->orderByDesc('transaction_date')
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        return response()->json([
            'status' => true,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'transactions' => $transactions
        ]);
    }

    // 📌 Add a new transaction
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'transaction_time' => 'nullable',
            'payment_method' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('transactions', 'public');
        }

        $transaction = Transaction::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Transaction added successfully',
            'data' => $transaction
        ]);
    }

    // 📌 Monthly summary
    public function summary($userId)
    {
        $monthly = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(amount) as total, type')
            ->where('user_id', $userId)
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get();

        return response()->json([
            'status' => true,
            'summary' => $monthly
        ]);
    }

    // 📌 Delete a transaction
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->attachment_path) {
            Storage::disk('public')->delete($transaction->attachment_path);
        }

        $transaction->delete();

        return response()->json([
            'status' => true,
            'message' => 'Transaction deleted successfully'
        ]);
    }

    // auto payments (Subscription)
    public function autoCreateTransaction()
    {
        if ($this->nextPaymentDate()->isToday()) {
            Transaction::create([
                'user_id' => $this->user_id,
                'category_id' => $this->category_id,
                'type' => 'expense',
                'amount' => $this->amount,
                'transaction_date' => now(),
                'payment_method' => 'auto',
                'notes' => "Auto payment for {$this->name} subscription",
            ]);
        }
    }
}
