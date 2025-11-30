<?php

namespace App\Services\Investments;

use App\Models\DematAccount;
use Carbon\Carbon;

class MutualFundPortfolioService
{
    /**
     * Calculate XIRR manually
     */
    function xirr(array $cashflows, array $dates, float $guess = 0.1, int $maxIterations = 100, float $tolerance = 1e-6): ?float
    {
        if (count($cashflows) !== count($dates) || empty($cashflows)) {
            return null;
        }

        // Convert dates to timestamps
        $timestamps = array_map(fn($d) => $d instanceof \DateTime ? $d->getTimestamp() : strtotime($d), $dates);

        $rate = $guess;
        $iteration = 0;

        try {
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

                if ($df == 0) return null; // derivative zero, cannot compute

                $newRate = $rate - $f / $df;

                if (abs($newRate - $rate) < $tolerance) return $newRate;

                $rate = $newRate;
                $iteration++;
            } while ($iteration < $maxIterations);

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Absolute return = (current value - cost) / cost
     */
    function absoluteReturnPct(float $cost, float $current): ?float
    {
        if ($cost == 0) return null;
        return ($current - $cost) / $cost * 100;
    }

    /**
     * Calculate user performance per folio
     */
    public function calculateUserPerformance($userId)
    {
        $result = [];

        $demats = DematAccount::where('user_id', $userId)
                    ->with(['folios.holdings.transactions'])
                    ->get();

        foreach ($demats as $demat) {
            foreach ($demat->folios as $folio) {
                $totalCost = 0;
                $totalCurrent = 0;
                $cashFlows = [];
                $dates = [];

                foreach ($folio->holdings as $holding) {
                    $totalCost += $holding->cost_value ?? 0;
                    $totalCurrent += $holding->current_value ?? 0;

                    foreach ($holding->transactions as $txn) {
                        $amount = strtoupper($txn->type) === 'BUY' ? -$txn->amount : $txn->amount;
                        $date = Carbon::parse($txn->transaction_date)->format('Y-m-d');

                        if (isset($cashFlows[$date])) {
                            $cashFlows[$date] += $amount;
                        } else {
                            $cashFlows[$date] = $amount;
                            $dates[] = $date;
                        }
                    }

                    $today = now()->format('Y-m-d');
                    if (isset($cashFlows[$today])) {
                        $cashFlows[$today] += $holding->current_value;
                    } else {
                        $cashFlows[$today] = $holding->current_value;
                        $dates[] = $today;
                    }
                }

                // Sort cashflows by date
                array_multisort($dates, $cashFlows);

                // Compute XIRR
                $xirr = null;
                try {
                    if ($totalCost > 0 && count($cashFlows) > 1) {
                        $xirr = $this->xirr(array_values($cashFlows), $dates);
                        $xirr = $xirr !== null ? round($xirr * 100, 2) : null;
                    }
                } catch (\Throwable $e) {
                    $xirr = null;
                }

                $absoluteReturn = round($totalCurrent - $totalCost, 2);
                $absoluteReturnPct = $this->absoluteReturnPct($totalCost, $totalCurrent);
                $absoluteReturnPct = $absoluteReturnPct !== null ? round($absoluteReturnPct, 2) : null;

                $result[] = [
                    'demat_id' => $demat->id,
                    'folio_id' => $folio->id,
                    'total_cost' => round($totalCost, 2),
                    'total_current' => round($totalCurrent, 2),
                    'absolute_return' => $absoluteReturn,
                    'absolute_return_pct' => $absoluteReturnPct,
                    'xirr' => $xirr
                ];
            }
        }

        return $result;
    }
}
