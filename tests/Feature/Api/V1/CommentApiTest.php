<?php

namespace Tests\Feature\Api\V1;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    #[Test]
    public function public_user_comments_are_pending_by_default()
    {
        $post = Post::create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'user_id' => User::factory()->create()->id,
            'status' => 'published'
        ]);

        $user = User::factory()->create();
        $user->assignRole('reader');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/posts/{$post->slug}/comments", [
                'body' => 'This is a pending comment'
            ]);

        $response->assertCreated()
            ->assertJsonFragment(['message' => 'Comment submitted for moderation.']);

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a pending comment',
            'status' => 'pending'
        ]);
    }

    #[Test]
    public function public_users_cannot_see_pending_comments()
    {
        $post = Post::create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'user_id' => User::factory()->create()->id,
            'status' => 'published'
        ]);

        Comment::create([
            'post_id' => $post->id,
            'user_id' => User::factory()->create()->id,
            'body' => 'Hidden Comment',
            'status' => 'pending'
        ]);

        $user = User::factory()->create();
        $user->assignRole('reader');

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/posts/{$post->slug}/comments")
            ->assertOk()
            ->assertJsonMissing(['body' => 'Hidden Comment']);
    }

    #[Test]
    public function admin_can_approve_comment()
    {
        $post = Post::create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'user_id' => User::factory()->create()->id,
            'status' => 'published'
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => User::factory()->create()->id,
            'body' => 'To Approve',
            'status' => 'pending'
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/comments/{$comment->id}", [
                'status' => 'approved'
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'approved'
        ]);
    }
}
