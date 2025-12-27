<?php

namespace App\Observers;

use App\Models\Post;
use App\Jobs\SendWebhookJob;
use App\Models\Webhook;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->triggerWebhooks('post.created', ['post' => $post->toArray()]);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->triggerWebhooks('post.updated', ['post' => $post->toArray()]);
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->triggerWebhooks('post.deleted', ['post' => $post->toArray()]);
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        $this->triggerWebhooks('post.restored', ['post' => $post->toArray()]);
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        $this->triggerWebhooks('post.forceDeleted', ['post' => $post->toArray()]);
    }



    protected function triggerWebhooks(string $eventName, $data): void
    {
        $webhooks = Webhook::where('event', $eventName)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            SendWebhookJob::dispatch(
                $webhook->url, 
                ['event' => $eventName, 'data' => $data], 
                $webhook->secret,
                $webhook->headers
            );
        }
    }
}
