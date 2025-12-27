<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    #[Test]
    public function public_users_can_only_see_published_posts()
    {
        $publishedPost = Post::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'content' => 'Content',
            'status' => 'published',
            'user_id' => User::factory()->create()->id
        ]);

        $draftPost = Post::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'content' => 'Content',
            'status' => 'draft',
            'user_id' => User::factory()->create()->id
        ]);

        // Reader (Authenticated but no special role)
        $user = User::factory()->create();
        $user->assignRole('reader');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/posts');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Published Post'])
            ->assertJsonMissing(['title' => 'Draft Post']);
    }

    #[Test]
    public function public_users_cannot_view_draft_post()
    {
        $draftPost = Post::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'content' => 'Content',
            'status' => 'draft',
            'user_id' => User::factory()->create()->id
        ]);

        $user = User::factory()->create();
        $user->assignRole('reader');

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/posts/{$draftPost->slug}")
            ->assertNotFound();
    }

    #[Test]
    public function admins_can_see_drafts()
    {
        $draftPost = Post::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'content' => 'Content',
            'status' => 'draft',
            'user_id' => User::factory()->create()->id
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/posts')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Draft Post']);

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/posts/{$draftPost->slug}")
            ->assertOk();
    }

    #[Test]
    public function admin_can_create_post()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'title' => 'New Analytics Post',
            'content' => 'Deep dive',
            'status' => 'published',
            'type' => 'article'
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/posts', $payload);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'New Analytics Post']);

        $this->assertDatabaseHas('posts', ['title' => 'New Analytics Post']);
    }
}
