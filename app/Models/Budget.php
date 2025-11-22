<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;

class Budget extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'start_date',
        'end_date',
        'is_recurring',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate total spent under this budget
    public function totalSpent()
    {
        $startDate = $this->start_date 
            ? Carbon::parse($this->start_date)->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = $this->end_date 
            ? Carbon::parse($this->end_date)->endOfDay() 
            : Carbon::now()->endOfMonth()->endOfDay();
            
        return $this->user->transactions()
            ->where('category_id', $this->category_id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function remaining()
    {
        return $this->amount - $this->totalSpent();
    }

    public function spendingPercentage()
    {
        if ($this->amount == 0) return 0;
        return ($this->totalSpent() / $this->amount) * 100;
    }

    public function getStatus() {
         $spent = $this->totalSpent(); // assuming totalSpent() already exists
        $budget = $this->amount;      // budget limit

        if ($spent >= $budget) {
            return 'over';             // exceeded budget
        } elseif ($spent >= ($budget * 0.75)) {
            return 'warning';          // nearing limit
        } else {
            return 'on track';             // under budget
        }
    }

    public function getBudgetMessage()
    {
        $spent = $this->totalSpent();
        $budget = $this->amount;
        $remaining = max($budget - $spent, 0);

        // Days left in current month
        $today = Carbon::now();
        $endOfMonth = Carbon::now()->endOfMonth();
        $daysLeft = $today->diffInDays($endOfMonth) + 1;

        // Daily allowance
        $dailyAllowance = $daysLeft > 0 ? round($remaining / $daysLeft, 2) : 0;

        // Friendly AI-style messages
        if ($spent == 0) {
            return "🎉 You haven't spent a penny in this category yet! You can spend up to ₹$dailyAllowance daily and still be a budget master.";
        }

        if ($spent >= $budget) {
            return "⚠️ Whoa! You've hit your budget limit of ₹$budget in this category. Time to pause and rethink your spending!";
        }

        if ($spent >= ($budget * 0.9)) {
            return "🚨 Almost there! You've spent ₹$spent out of ₹$budget. Try to spend less than ₹$dailyAllowance per day to stay safe.";
        }

        if ($spent >= ($budget * 0.75)) {
            return "⚡ Careful! ₹$spent spent out of ₹$budget. Keep your daily spending under ₹$dailyAllowance to finish the month smoothly.";
        }

        if ($spent >= ($budget * 0.5)) {
            return "🙂 You're doing good! ₹$spent spent out of ₹$budget. You can comfortably spend around ₹$dailyAllowance per day to stay on track.";
        }

        return "👍 Great start! You've spent ₹$spent out of ₹$budget. You can spend ₹$dailyAllowance daily for the remaining $daysLeft days and finish the month smartly.";
    }
    public static function getTotalBudgetMessage($totalSpent, $totalBudget, $dailyAllowance)
    {
        $remaining = max($totalBudget - $totalSpent, 0);
        $today = Carbon::now();
        $daysLeft = $today->diffInDays(Carbon::now()->endOfMonth()) + 1;

        if ($totalBudget == 0) {
            return "❗ You haven't set any budget for this month yet.";
        }

        if ($totalSpent == 0) {
            return "🎉 Great start! No spending so far. You can spend ₹$dailyAllowance per day and stay perfectly on track.";
        }

        if ($totalSpent >= $totalBudget) {
            return "⚠️ You've exceeded your total budget of ₹$totalBudget. Time to slow down this month.";
        }

        if ($totalSpent >= ($totalBudget * 0.9)) {
            return "🚨 You've spent ₹$totalSpent of ₹$totalBudget. Try staying under ₹$dailyAllowance/day.";
        }

        if ($totalSpent >= ($totalBudget * 0.75)) {
            return "⚡ You're nearing your monthly limit. Aim for ₹$dailyAllowance/day to finish the month safely.";
        }

        if ($totalSpent >= ($totalBudget * 0.5)) {
            return "🙂 You're halfway through your monthly budget. Stick to ₹$dailyAllowance per day.";
        }

        return "👍 You're off to a good start! You can spend ₹$dailyAllowance daily for the remaining $daysLeft days.";
    }

}
