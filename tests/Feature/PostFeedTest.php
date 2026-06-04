<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDO;
use Tests\TestCase;

final class PostFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $testDatabaseConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? null;
        if (! in_array('sqlite', PDO::getAvailableDrivers(), true) && $testDatabaseConnection === 'sqlite') {
            self::markTestSkipped('The pdo_sqlite extension is not installed in this PHP runtime.');
        }

        parent::setUp();
    }

    public function test_authenticated_user_can_manage_post_likes_and_comments(): void
    {
        $user = User::factory()->create();

        $createResponse = $this
            ->actingAs($user)
            ->post(route('posts.store'), [
                'title' => 'First Laravel post',
                'content' => 'Body from the feed',
            ]);

        $createResponse->assertRedirect();
        $post = Post::query()->firstOrFail();
        $this->assertSame('First Laravel post', $post->title);
        $this->assertSame('Body from the feed', $post->content);
        $this->assertSame($user->id, $post->user_id);

        $this
            ->actingAs($user)
            ->put(route('posts.update', $post), [
                'title' => 'Updated post',
                'content' => 'Updated body',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated post',
            'content' => 'Updated body',
        ]);

        $this
            ->actingAs($user)
            ->post(route('posts.like', $post))
            ->assertRedirect();

        $this->assertSame(1, PostLike::query()->count());

        $this
            ->actingAs($user)
            ->post(route('posts.like', $post))
            ->assertRedirect();

        $this->assertSame(0, PostLike::query()->count());

        $this
            ->actingAs($user)
            ->post(route('posts.comments.store', $post), ['body' => 'Useful comment'])
            ->assertRedirect();

        $comment = Comment::query()->firstOrFail();
        $this->assertSame('Useful comment', $comment->body);

        $this
            ->actingAs($user)
            ->delete(route('comments.destroy', $comment))
            ->assertRedirect();

        $this->assertSame(0, Comment::query()->count());
    }

    public function test_non_owner_cannot_update_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::query()->create([
            'title' => 'Owned',
            'content' => 'Private edit',
            'user_id' => $owner->id,
        ]);

        $this
            ->actingAs($other)
            ->put(route('posts.update', $post), [
                'title' => 'Hacked',
                'content' => 'Changed',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Owned',
        ]);
    }
}
