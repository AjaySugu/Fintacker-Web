<?php

namespace App\Services\Banking;
use App\Models\UserAccount;

class UserAccountService
{

   public function createUserAccount(array $data, int $userId)
   {
      try {
         DB::beginTransaction();

         $account = new UserAccount();

         $account->user_id = $userId;
         $account->account_name = $data['account_name'];
         $account->account_type = $data['account_type'];
         $account->institution_name = $data['institution_name'] ?? null;
         $account->account_number = $data['account_number'] ?? null;
         $account->current_balance = $data['current_balance'] ?? 0;
         $account->credit_limit = $data['credit_limit'] ?? null;
         $account->is_synced = $data['is_synced'] ?? false;
         $account->sync_metadata = $data['sync_metadata'] ?? null;
         $account->save();

         DB::commit();
         return ['status' => true, 'message' => 'User account created successfully'];
      } catch (Exception $e) {
         DB::rollBack();
         \Log::error('UserAccountService->createUserAccount ' . $e->getMessage());
         return ['status' => false, 'message' => 'Failed to create user account'];
      }
   }
}