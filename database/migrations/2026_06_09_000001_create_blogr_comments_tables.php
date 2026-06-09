<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogr_comments', function (Blueprint $table) {
            $table->id();
            $table->string('post_slug');
            $table->foreignId('parent_id')->nullable()->constrained('blogr_comments')->cascadeOnDelete();
            $table->string('author_name');
            $table->string('author_email');
            $table->text('content');
            $table->text('content_html')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, spam
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('vote_score')->default(0);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index('post_slug');
            $table->index('status');
        });

        Schema::create('blogr_comment_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('blogr_comments')->cascadeOnDelete();
            $table->tinyInteger('vote_type'); // 1 = up, -1 = down
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('comment_id');
        });

        Schema::create('blogr_comment_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('blogr_comments')->cascadeOnDelete();
            $table->string('email');
            $table->string('token');
            $table->timestamps();

            $table->index('comment_id');
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogr_comment_subscriptions');
        Schema::dropIfExists('blogr_comment_votes');
        Schema::dropIfExists('blogr_comments');
    }
};
