<?php

namespace App\Http\Controllers\V1\Investments;

use App\Http\Controllers\Controller;
use App\Models\SetuConsent;
use App\Services\Investments\ConsentService;
use App\Services\Investments\EquityPortfolioService;
use App\Services\Investments\MutualFundPortfolioService;
use Illuminate\Http\Request;

class ConsentsController extends Controller
{

    protected $consentService;
    protected $mfPortfolioService;
    protected $stocksPortfolioService;

    public function __construct(ConsentService $consentService, MutualFundPortfolioService $mfPortfolioService, EquityPortfolioService $stocksPortfolioService)
    {
        $this->consentService = $consentService;
        $this->mfPortfolioService = $mfPortfolioService;
        $this->stocksPortfolioService = $stocksPortfolioService;
    }

    public function index() {
        return view('layouts.app.Investments.consents');
    }

    public function createconsents(Request $request) {
        // $userId = $request->user()->id;
        $userId = 2;
        // $pan = $request->pan_no;
        $pan = "QWEPR1234E";
        $vua = 9941944838 . "@finvu";

        $dataRangeFrom = now()->startOfDay();
        $dataRangeTo   = now()->addYear()->subDay()->endOfDay();

        $consentData = [
            'PAN' => $pan,
            'consentDuration' => [
                'unit' => 'YEAR',
                'value' => 1
            ],
            'fetchType' => 'PERIODIC',
            'frequency' => [
                'unit' => 'DAY',
                'value' => 1
            ],
            "consentTypes"=> ["PROFILE", "SUMMARY", "TRANSACTIONS"],
            'fiTypes' => ["MUTUAL_FUNDS", "EQUITIES" ],
            'vua' => $vua,
            'purpose'=> [
                'code'=> '101',
                'text'=> "Wealth management service - track user's MF holdings",
                'category' => [
                    'type' => 'Wealth'
                ],
            'refUri' => "https://api.rebit.org.in/aa/purpose/101.xml"
            ],
            'dataLife'=> [
                'unit'=> 'DAY',
                'value'=> 0
            ],
            'dataRange'=> [
                'from'=> $dataRangeFrom->toIso8601String(),
                'to'=> $dataRangeTo->toIso8601String()
            ],
            'enableAdditionalPhoneNumber'=> true,

            // 'consentStart' => now()->toIso8601String(),
            // 'consentExpiry' => now()->addDays(1)->toIso8601String(),
        ];

        $result = $this->consentService->createConsent($userId, $consentData);
        // dd($result);
        if (isset($result['status']) && $result['status'] === true) {
            return response()->json(['status' => 200, 'redirect_url' => $result['consent']->redirect_url]);
        } else {
            return response()->json($result);
        }
        return response()->json($result);
    }

    public function getUserMFProtifilio($userId) {
        $result = $this->mfPortfolioService->calculateUserPerformance($userId);
        return response($result);
    } 

    public function getUserStocksProtifilio($userId) {
        $result = $this->stocksPortfolioService->calculateUserPortfolio($userId);
        return response($result);
    } 
}
