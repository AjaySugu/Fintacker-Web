<?php
namespace App\Services\Investments;

use App\Models\DematAccount;
use App\Models\EquityHolding;
use App\Models\EquityTransaction;
use Carbon\Carbon;

class EquityPortfolioService
{
    /**
     * Calculate XIRR manually
     */
    public function xirr(array $cashflows, array $dates, float $guess = 0.1, int $maxIterations = 100, float $tolerance = 1e-6): ?float
    {
        if (count($cashflows) !== count($dates)) {
            throw new \Exception("Cashflows and dates must have the same length");
        }

        $timestamps = array_map(function ($d) {
            return $d instanceof \DateTime ? $d->getTimestamp() : strtotime($d);
        }, $dates);

        $rate = $guess;
        $iteration = 0;

        do {
            $f = 0.0;
            $df = 0.0;
            $t0 = $timestamps[0];

            foreach ($cashflows as $i => $cf) {
                $t = ($timestamps[$i] - $t0) / (365.0 * 24 * 60 * 60);
                $denom = pow(1 + $rate, $t);
                $f += $cf / $denom;
                if ($denom != 0) {
                    $df += -$t * $cf / ($denom * (1 + $rate));
                }
            }

            if ($df == 0) return null;

            $newRate = $rate - $f / $df;

            if (abs($newRate - $rate) < $tolerance) {
                return $newRate;
            }

            $rate = $newRate;
            $iteration++;
        } while ($iteration < $maxIterations);

        return null;
    }

    /**
     * Absolute return
     */
    public function absoluteReturn(float $cost, float $current): float
    {
        if ($cost == 0) return 0;
        return ($current - $cost) / $cost;
    }

    /**
     * Calculate equity portfolio metrics per user
     */
    public function calculateUserPortfolio($userId)
    {
        $result = [];
        $demats = DematAccount::where('user_id', $userId)
                    ->with(['holdings.transactions'])
                    ->get();

        foreach ($demats as $demat) {
            $totalInvested = 0;
            $totalCurrent = 0;
            $totalGain = 0;
            $totalLoss = 0;
            $cashFlows = [];
            $dates = [];

            foreach ($demat->holdings as $holding) {
                $cost = $holding->investment_value ?? 0;
                $current = $holding->current_value ?? 0;
                $totalInvested += $cost;
                $totalCurrent += $current;

                if ($current > $cost) $totalGain += ($current - $cost);
                else $totalLoss += ($cost - $current);

                // Cashflows for XIRR
                foreach ($holding->transactions as $txn) {
                    $amount = strtoupper($txn->txn_type) === 'BUY' ? -$txn->trade_value : $txn->trade_value;
                    $txnDate = Carbon::parse($txn->transaction_date_time)->format('Y-m-d');
                    $cashFlows[$txnDate] = ($cashFlows[$txnDate] ?? 0) + $amount;
                }

                // Add current value as cashflow for today
                $today = now()->format('Y-m-d');
                $cashFlows[$today] = ($cashFlows[$today] ?? 0) + $current;
            }

            // Prepare for XIRR
            if (count($cashFlows) > 1) {
                $dates = array_keys($cashFlows);
                $flows = array_values($cashFlows);
                try {
                    $xirr = $this->xirr($flows, $dates);
                } catch (\Exception $e) {
                    $xirr = null;
                }
            } else {
                $xirr = null;
            }

            // Daily change: compare with previous day close if stored in your data
            $dailyChange = 0;
            foreach ($demat->holdings as $holding) {
                if (isset($holding->last_traded_price) && isset($holding->prev_close_price)) {
                    $dailyChange += ($holding->last_traded_price - $holding->prev_close_price) * $holding->units;
                }
            }

            $result[] = [
                'demat_id' => $demat->id,
                'total_invested' => round($totalInvested, 2),
                'total_current' => round($totalCurrent, 2),
                'total_gain' => round($totalGain, 2),
                'total_loss' => round($totalLoss, 2),
                'absolute_return_pct' => $totalInvested > 0 ? round(($totalCurrent - $totalInvested)/$totalInvested * 100, 2) : null,
                'xirr' => $xirr !== null ? round($xirr * 100, 2) : null,
                'daily_change' => round($dailyChange, 2)
            ];
        }

        return $result;
    }
}
