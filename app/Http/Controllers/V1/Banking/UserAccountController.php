<?php

namespace App\Http\Controllers\V1\Banking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Banking\UserAccountService;

class UserAccountController extends Controller
{
    protected $userAccountService;

    public function __construct(UserAccountService $userAccountService)
    {
        $this->userAccountService = $userAccountService;
    }

    public function getUserAccounts(Request $request)
    {
        try {
            $user = $request->user();
            $accounts = $user->accounts;
            return response()->json(['status' => true, 'data' => $accounts]);
        } catch (\Exception $e) {
            \Log::error('UserAccountController->getUserAccounts ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while fetching user accounts']);
        }
    }

    public function createUserAccount(Request $request)
    {
        try {
            $createdAccount = $this->userAccountService->createUserAccount($request->all(), $request->user()->id);
            if ($createdAccount['status'] === true) {
                return response()->json(['status' => true, 'message' => $createdAccount['message']]);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to create user account']);
            }
        } catch (\Exception $e) {
            \Log::error('UserAccountController->createUserAccount ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while creating the user account']);
        }
        
    }
}
