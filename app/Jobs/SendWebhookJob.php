<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWebhookJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $url,
        public array $payload,
        public ?string $secret = null,
        public ?array $headers = null
    ) {}

    /**
     * Execute the job.
     */
   public function handle(): void
    {
        $defaultHeaders = [
            'X-Webhook-Secret' => $this->secret,
            'Content-Type' => 'application/json',
        ];

        $finalHeaders = array_merge($defaultHeaders, $this->headers ?? []);

        Http::withHeaders($finalHeaders)->post($this->url, $this->payload);
    }
}
