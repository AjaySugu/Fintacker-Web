<?php

namespace App\Jobs;

use App\Services\Investments\ConsentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchUserDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $sessionId;
    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $sessionId, int $userId)
    {
        $this->sessionId = $sessionId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Call your service
        app(ConsentService::class)->fetchUserData($this->sessionId, $this->userId);
    }
}
