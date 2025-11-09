<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Notification;
use App\Notifications\SubscriptionReminder;

class SendSubscriptionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription payment reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        $subscriptions = Subscription::where('status','active')->get();

        foreach($subscriptions as $sub){
            $message = $sub->generateReminder();
            if($message){
                $sub->user->notify(new SubscriptionReminder($message));
            }
        }

        $this->info('Subscription reminders sent successfully!');
    }
}
