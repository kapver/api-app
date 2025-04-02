<?php

namespace App\Jobs;

use App\Services\PhotoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class OptimizePhotoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $path)
    {
        Log::debug(__METHOD__, ['path' => $path]);
    }

    /**
     * Execute the job.
     */
    public function handle(PhotoService $photoService): void
    {
        Log::debug(__METHOD__, ['path' => $this->path]);
        $photoService->optimize($this->path);
    }
}
