<?php

namespace Happytodev\BlogrComments\Tests\Feature;

use Happytodev\BlogrComments\Models\Comment;
use Happytodev\BlogrComments\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class CommentLoadingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    #[Test]
    public function comments_api_returns_comments_for_a_post_slug(): void
    {
        Comment::create([
            'post_slug' => 'test-post',
            'author_name' => 'John',
            'author_email' => 'john@example.com',
            'content' => 'Hello world',
            'content_html' => '<p>Hello world</p>',
            'status' => 'approved',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->getJson('/comments/test-post');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'comments' => [
                    '*' => ['id', 'author_name', 'content', 'content_html', 'vote_score', 'replies'],
                ],
                'total',
            ])
            ->assertJsonCount(1, 'comments');
    }

    #[Test]
    public function comments_api_returns_only_approved_comments(): void
    {
        Comment::create([
            'post_slug' => 'test-post',
            'author_name' => 'John',
            'author_email' => 'john@example.com',
            'content' => 'Approved',
            'content_html' => '<p>Approved</p>',
            'status' => 'approved',
            'ip_address' => '127.0.0.1',
        ]);

        Comment::create([
            'post_slug' => 'test-post',
            'author_name' => 'Jane',
            'author_email' => 'jane@example.com',
            'content' => 'Pending',
            'content_html' => '<p>Pending</p>',
            'status' => 'pending',
            'ip_address' => '127.0.0.1',
        ]);

        Comment::create([
            'post_slug' => 'test-post',
            'author_name' => 'Spammy',
            'author_email' => 'spam@example.com',
            'content' => 'Spam',
            'content_html' => '<p>Spam</p>',
            'status' => 'spam',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->getJson('/comments/test-post');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'comments')
            ->assertJsonPath('comments.0.author_name', 'John');
    }

    #[Test]
    public function comments_api_returns_comments_sorted_by_newest(): void
    {
        // Use DB::table to bypass $fillable (created_at is not mass-assignable)
        DB::table('blogr_comments')->insert([
            'post_slug' => 'test-post', 'author_name' => 'First', 'author_email' => 'a@a.com',
            'content' => 'Oldest', 'content_html' => '<p>Oldest</p>', 'status' => 'approved',
            'ip_address' => '127.0.0.1', 'vote_score' => 0,
            'created_at' => now()->subDay(), 'updated_at' => now(),
        ]);
        DB::table('blogr_comments')->insert([
            'post_slug' => 'test-post', 'author_name' => 'Second', 'author_email' => 'b@b.com',
            'content' => 'Newest', 'content_html' => '<p>Newest</p>', 'status' => 'approved',
            'ip_address' => '127.0.0.1', 'vote_score' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $response = $this->getJson('/comments/test-post?sort=newest');

        $response->assertStatus(200)
            ->assertJsonPath('comments.0.author_name', 'Second')
            ->assertJsonPath('comments.1.author_name', 'First');
    }

    #[Test]
    public function comments_api_returns_comments_sorted_by_best(): void
    {
        $low = Comment::create([
            'post_slug' => 'test-post',
            'author_name' => 'Low',
            'author_email' => 'a@a.com',
            'content' => 'Low score',
            'content_html' => '<p>Low score</p>',
            'status' => 'approved',
            'ip_address' => '127.0.0.1',
            'vote_score' => 1,
        ]);

        $high = Comment::create([
            'post_slug' => 'test-post',
            'author_name' => 'High',
            'author_email' => 'b@b.com',
            'content' => 'High score',
            'content_html' => '<p>High score</p>',
            'status' => 'approved',
            'ip_address' => '127.0.0.1',
            'vote_score' => 5,
        ]);

        $response = $this->getJson('/comments/test-post?sort=best');

        $response->assertStatus(200)
            ->assertJsonPath('comments.0.author_name', 'High')
            ->assertJsonPath('comments.1.author_name', 'Low');
    }
}
