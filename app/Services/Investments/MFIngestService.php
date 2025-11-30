<?php

namespace App\Services\Investments;

use App\Models\MFFolio;
use App\Models\MFFolioHolding;
use App\Models\MFNavHistory;
use App\Models\MFScheme;
use App\Models\MFTransaction;
use Carbon\Carbon;
use DB;
use Log;

class MFIngestService
{
    public function ingestSetuPayload(array $payload, $userId = null)
    {
        DB::beginTransaction();
        try {
            $fips = $payload['fips'] ?? [];

            foreach ($fips as $fip) {
                foreach ($fip['accounts'] as $acct) {
                    $acctData = $acct['data']['account'] ?? $acct['data'] ?? $acct;

                    // Save folio
                    $folio = MFFolio::updateOrCreate(
                        [
                            'demat_account_id' => $userId,
                            'folio_no' => $acctData['maskedFolioNo'] ?? 'UNKNOWN'
                        ],
                        [
                            'masked_folio_no' => $acctData['maskedFolioNo'] ?? null,
                            'pan' => data_get($acctData, 'profile.holders.holder.0.pan'),
                            'profile_type' => data_get($acctData, 'profile.holders.type'),
                            'holders' => json_encode(data_get($acctData, 'profile.holders.holder', [])),
                            'raw' => json_encode($acctData)
                        ]
                    );

                    // Holdings
                    $holdings = data_get($acctData, 'summary.investment.holdings.holding', []);
                    if ($holdings && !is_array($holdings)) $holdings = [$holdings];

                    foreach ($holdings as $h) {
                        $scheme = MFScheme::updateOrCreate(
                            ['scheme_code' => $h['schemeCode'] ?? 'UNKNOWN'],
                            [
                                'amc' => $h['amc'] ?? null,
                                'scheme_plan' => $h['schemeOption'] ?? null,
                                'scheme_category' => $h['schemeCategory'] ?? null,
                                'isin' => $h['isin'] ?? null,
                                'isin_description' => $h['isinDescription'] ?? null,
                                'ucc' => $h['ucc'] ?? null,
                                'amfi_code' => $h['amfiCode'] ?? null,
                                'registrar' => $h['registrar'] ?? null,
                                'metadata' => json_encode($h)
                            ]
                        );

                        $folioHolding = MFFolioHolding::updateOrCreate(
                            ['folio_id' => $folio->id, 'scheme_id' => $scheme->id],
                            [
                                'units' => $h['closingUnits'] ?? 0,
                                'lien_units' => $h['lienUnits'] ?? 0,
                                'lockin_units' => $h['lockinUnits'] ?? 0,
                                'nav' => $h['nav'] ?? 0,
                                'nav_date' => isset($h['navDate']) ? Carbon::parse($h['navDate'])->toDateString() : now()->toDateString(),
                                'cost_value' => data_get($acctData, 'summary.costValue', 0),
                                'current_value' => data_get($acctData, 'summary.currentValue', 0),
                                'fatca_status' => $h['fatcaStatus'] ?? null,
                                'raw' => json_encode($h)
                            ]
                        );

                        // NAV History – safely handle duplicates
                        if (isset($h['navDate'])) {
                            MFNavHistory::updateOrCreate(
                                [
                                    'folio_holding_id' => $folioHolding->id,
                                    'nav_date' => Carbon::parse($h['navDate'])->toDateString()
                                ],
                                ['nav' => $h['nav'] ?? 0]
                            );
                        }

                        // Transactions
                        $transactions = data_get($acctData, 'transactions.transaction', []);
                        if ($transactions && !is_array($transactions)) $transactions = [$transactions];

                        foreach ($transactions as $t) {
                            $txnDate = null;
                            if (!empty($t['transactionDateTime'])) {
                                $txnDate = Carbon::parse($t['transactionDateTime']);
                            } elseif (!empty($t['transactionDate'])) {
                                $txnDate = Carbon::parse($t['transactionDate']);
                            }

                            MFTransaction::updateOrCreate(
                                [
                                    'folio_id' => $folio->id,
                                    'txn_id' => $t['txnId'] ?? null
                                ],
                                [
                                    'scheme_id' => $scheme->id,
                                    'txn_type' => substr($t['type'] ?? '', 0, 50), // prevent truncation
                                    'mode' => substr($t['mode'] ?? '', 0, 50),
                                    'units' => isset($t['units']) ? (float)$t['units'] : 0,
                                    'amount' => isset($t['amount']) ? (float)$t['amount'] : 0,
                                    'nav' => isset($t['nav']) ? (float)$t['nav'] : 0,
                                    'transaction_date' => $txnDate,
                                    'narration' => substr($t['narration'] ?? '', 0, 255),
                                    'raw' => json_encode($t)
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('MF ingest error: ' . $e->getMessage(), ['payload' => $payload]);
            return false;
        }
    }
}
