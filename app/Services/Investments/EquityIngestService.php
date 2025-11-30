<?php
namespace App\Services\Investments;

use App\Models\DematAccount;
use App\Models\EquityHolding;
use App\Models\EquityTransaction;
use App\Models\EquityCompany;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;

class EquityIngestService
{
    public function ingestSetuPayload(array $payload, $userId = null)
    {
        // payload structure assumed similar to your pasted JSON (top-level: id, consentId, fips => [ { fipID, accounts => [ { data => { account => { ... }}}]}])
       DB::beginTransaction();

        try {
            $fips = $payload['fips'] ?? [];

            foreach ($fips as $fip) {
                foreach ($fip['accounts'] as $acct) {

                    // Defensive extraction
                    $acctData = $acct['data']['account'] ?? $acct['data'] ?? $acct;

                    Log::info('Raw Account JSON:', $acctData);

                    $linkRef = $acctData['linkedAccRef'] ?? null;
                    $masked = $acctData['maskedAccNumber'] ?? null;

                    // Save Demat Account
                    $demat = DematAccount::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'consent_id' => $payload['consentId'] ?? null,
                            'link_ref' => $linkRef,
                            'masked_acc_number' => $masked,
                        ],
                        [
                            'masked_demat_id' => $acctData['maskedDematID'] ?? null,
                            'fip_id' => $fip['fipID'] ?? null,
                            'broker_name' => $acctData['profile']['holders']['holder'][0]['brokerName'] ?? null,
                            'status' => $acct['FIstatus'] ?? 'READY',
                            'raw_response' => json_encode($acctData),
                        ]
                    );

                    // ======================
                    // Holdings
                    // ======================
                    $holdings = data_get($acctData, 'summary.investment.holdings.holding', []);

                    if ($holdings && !is_array($holdings)) {
                        $holdings = [$holdings];
                    }

                    foreach ($holdings as $h) {
                        $isin = $h['isin'] ?? null;
                        $issuer = $h['issuerName'] ?? $h['issuer'] ?? $h['isinDescription'] ?? null;
                        $units = isset($h['units']) ? (float)$h['units'] : (float)($h['closingUnits'] ?? 0);
                        $ltp = isset($h['lastTradedPrice']) ? (float)$h['lastTradedPrice'] : (float)($h['last_traded_price'] ?? $h['nav'] ?? 0);
                        $invValue = isset($acctData['summary']['investmentValue']) ? (float)$acctData['summary']['investmentValue'] : (float)($h['investmentValue'] ?? 0);
                        $currentValue = isset($acctData['summary']['currentValue']) ? (float)$acctData['summary']['currentValue'] : (float)($h['currentValue'] ?? 0);

                        // Save or update company
                        if ($isin) {
                            $company = EquityCompany::firstOrCreate(
                                ['isin' => $isin],
                                [
                                    'issuer_name' => $issuer,
                                    'symbol' => $h['symbol'] ?? null,
                                    'metadata' => json_encode($h),
                                ]
                            );
                            $companyId = $company->id;
                        } else {
                            $companyId = null;
                        }

                        EquityHolding::updateOrCreate(
                            ['demat_account_id' => $demat->id, 'isin' => $isin],
                            [
                                'company_id' => $companyId,
                                'symbol' => $h['symbol'] ?? null,
                                'issuer_name' => $issuer,
                                'units' => $units,
                                'avg_rate' => $h['rate'] ?? $h['avgRate'] ?? 0,
                                'last_traded_price' => $ltp,
                                'investment_value' => $invValue,
                                'current_value' => $currentValue,
                                'raw' => json_encode($h),
                            ]
                        );
                    }

                    // ======================
                    // Transactions
                    // ======================
                    $transactions = data_get($acctData, 'transactions.transaction', []);

                    if ($transactions && !is_array($transactions)) {
                        $transactions = [$transactions];
                    }

                    foreach ($transactions as $t) {
                        $txnId = $t['txnId'] ?? $t['orderId'] ?? null;
                        $txndt = null;

                        if (!empty($t['transactionDateTime'])) {
                            $txndt = Carbon::parse($t['transactionDateTime'])->toDateTimeString();
                        } elseif (!empty($t['transactionDate'])) {
                            $txndt = Carbon::parse($t['transactionDate'])->toDateTimeString();
                        }

                        // Deduplicate
                        $existsQuery = EquityTransaction::query()->where('demat_account_id', $demat->id);
                        if ($txnId) {
                            $existsQuery->where('txn_id', $txnId);
                        } else {
                            $existsQuery->where('transaction_date_time', $txndt)
                                        ->where('isin', $t['isin'] ?? null)
                                        ->where('units', $t['units'] ?? null);
                        }

                        if ($existsQuery->exists()) continue;

                        EquityTransaction::create([
                            'demat_account_id' => $demat->id,
                            'txn_id' => $txnId,
                            'txn_type' => $t['type'] ?? null,
                            'instrument_type' => $t['instrumentType'] ?? null,
                            'exchange' => $t['exchange'] ?? null,
                            'isin' => $t['isin'] ?? null,
                            'symbol' => $t['symbol'] ?? null,
                            'company_name' => $t['companyName'] ?? null,
                            'units' => isset($t['units']) ? (float)$t['units'] : null,
                            'rate' => isset($t['rate']) ? (float)$t['rate'] : null,
                            'trade_value' => isset($t['tradeValue']) ? (float)$t['tradeValue'] : null,
                            'other_charges' => isset($t['otherCharges']) ? (float)$t['otherCharges'] : null,
                            'total_charge' => isset($t['totalCharge']) ? (float)$t['totalCharge'] : null,
                            'transaction_date_time' => $txndt,
                            'narration' => $t['narration'] ?? null,
                            'raw' => json_encode($t),
                        ]);
                    }
                }
            }

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Equity ingest error: ' . $e->getMessage(), ['payload' => $payload]);
            return false;
        }
    }
}
