<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'category_id',
        'start_date',
        'frequency',
        'auto_pay',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Calculate next payment date based on frequency
     */
    public function nextPaymentDate()
    {
        $today = Carbon::now();
    $startDate = Carbon::parse($this->start_date);
    $lastPaid = $this->last_paid ? Carbon::parse($this->last_paid) : null;
    $endDate = $this->end_date ? Carbon::parse($this->end_date) : null;

    // Base date is last paid or start date if never paid
    $baseDate = $lastPaid ?? $startDate;

    $nextPayment = $baseDate->copy();

    switch ($this->frequency) {
        case 'daily':
            $diff = $lastPaid ? $lastPaid->diffInDays($today) : 0;
            $nextPayment = $baseDate->copy()->addDays(max($diff, 1));
            break;

        case 'weekly':
            $diff = $lastPaid ? $lastPaid->diffInWeeks($today) : 0;
            $nextPayment = $baseDate->copy()->addWeeks(max($diff, 1));
            break;

        case 'monthly':
            if ($lastPaid) {
                $diff = $lastPaid->diffInMonths($today);
                $nextPayment = $baseDate->copy()->addMonths(max($diff, 1));
            } else {
                $diff = $startDate->diffInMonths($today);
                $nextPayment = $startDate->copy()->addMonths($diff);
            }
            break;

        case 'yearly':
            if ($lastPaid) {
                $diff = $lastPaid->diffInYears($today);
                $nextPayment = $baseDate->copy()->addYears(max($diff, 1));
            } else {
                $diff = $startDate->diffInYears($today);
                $nextPayment = $startDate->copy()->addYears($diff);
            }
            break;
        default:
            $nextPayment = $baseDate->copy();
    }

    // If nextPayment exceeds end_date, return null
    if ($endDate && $nextPayment->gt($endDate)) {
        return null;
    }

    return $nextPayment;
    }

    /**
     * Generate AI-style reminder message
     */
    public function generateReminder()
    {
        $today = Carbon::now();
        $nextPayment = $this->nextPaymentDate();
        // dd($nextPayment);
        $daysLeft = $today->diffInDays($nextPayment, false);

        $subscriptionName = $this->name ?? 'subscription';
        $budgetImpact = $this->calculateBudgetImpact();
        $categoryName = optional($this->category)->name ?? $this->name ?? 'subscription';
        $amount = number_format($this->amount, 2);
        // dd($daysLeft);
        // print $daysLeft;
        return match (true) {
            $daysLeft === 7 => "🕒 Heads up! Your {$categoryName} - {$subscriptionName} subscription of ₹{$amount} is due in 7 days. {$budgetImpact}",
            $daysLeft === 3 => "⏳ 3 days left! {$categoryName} - {$subscriptionName} needs ₹{$amount}. {$budgetImpact}",
            $daysLeft === 1 => "🚨 Tomorrow is your {$categoryName} - {$subscriptionName} payment of ₹{$amount}. Don’t forget! {$budgetImpact}",
            $daysLeft === 0 => "⚡ Today! Pay {$categoryName} - {$subscriptionName} of ₹{$amount} to stay on track. {$budgetImpact}",
            $daysLeft < 0 && abs($daysLeft) <= 3 =>
                "⚠️ {$categoryName} - {$subscriptionName} payment was due " . abs($daysLeft) . " days ago. Let's get back on track!",
            default => null,
        };
    }

    /**
     * Example budget impact calculation
     */
    protected function calculateBudgetImpact()
    {
        $user = $this->user;
        if (!$user || !method_exists($user, 'budgets')) {
            return ""; // avoid crash if user->budgets() not defined yet
        }

        $budget = $user->budgets()
            ->where('category_id', $this->category_id)
            ->where('status', 'active')
            ->first();

        if (!$budget) return "";

        $spent = $budget->totalSpent();
        $remaining = max($budget->amount - $spent - $this->amount, 0);

        $daysLeft = Carbon::now()->endOfMonth()->diffInDays(Carbon::now()) + 1;
        $dailyAllowance = $daysLeft > 0 ? round($remaining / $daysLeft, 2) : 0;

        $categoryName = optional($this->category)->name ?? 'this category';
        return "After paying, you can spend ₹{$dailyAllowance}/day in {$categoryName} until month-end.";
    }

    public function renewIfAutoPayEnabled()
    {
        if ($this->auto_pay && $this->nextPaymentDate()->isToday()) {
            $this->update(['start_date' => Carbon::now()]);
        }
    }
}
