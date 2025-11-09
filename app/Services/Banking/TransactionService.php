<?php

namespace App\Services\Banking;
use App\Models\Transaction;
use App\Models\UserAccount;

class TransactionService
{

   public function createTransaction(array $data, int $userId)
   {
      try {
         DB::beginTransaction();
         $transaction = new Transaction();

         $transaction->user_id = $userId;
         $transaction->category_id = $data['category_id'];
         $transaction->account_id = $data['account_id'];
         $transaction->type = $data['type'];
         $transaction->amount = $data['amount'];
         $transaction->transaction_date = $data['transaction_date'];
         $transaction->transaction_time = $data['transaction_time'] ?? null;
         $transaction->payment_method = $data['payment_method'] ?? null;
         $transaction->tags = isset($data['tags']) ? json_encode($data['tags']) : null;
         $transaction->notes = $data['notes'] ?? null;
         $transaction->save();

         if ($transaction) {
            $account = UserAccount::findOrFail($data['account_id']);
               if ($data['type'] === 'income') {
                  $account->current_balance += $data['amount'];
               } else {
                  $account->current_balance -= $data['amount'];
               }
               $account->save();
            DB::commit();
            return ['status' => true, 'message' => 'Transaction created successfully'];
         }
      } catch (Exception $e) {
         DB::rollBack();
         return response()->json(['status'=> false,'message' => 'Failed to create transaction'], 500);
         \Log::error('TransactionService->createTransaction ' . $e->getMessage());
      }
   }
}  
