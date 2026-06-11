<?php

namespace Happytodev\BlogrComments\Tests\Feature;

use Happytodev\BlogrComments\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommentsViewTest extends TestCase
{
    #[Test]
    public function alpine_component_has_correct_nav_config_defaults(): void
    {
        $html = view('blogr-comments::comments', [
            'postSlug' => 'test-post',
            'comments' => collect([]),
            'sort' => 'newest',
        ])->render();

        $this->assertStringContainsString('navEnabled: true', $html);
        $this->assertStringContainsString('navSticky: true', $html);
    }

    #[Test]
    public function alpine_component_reflects_disabled_nav(): void
    {
        config(['blogr.ui.navigation.enabled' => false]);

        $html = view('blogr-comments::comments', [
            'postSlug' => 'test-post',
            'comments' => collect([]),
            'sort' => 'newest',
        ])->render();

        $this->assertStringContainsString('navEnabled: false', $html);
    }

    #[Test]
    public function alpine_component_reflects_non_sticky_nav(): void
    {
        config(['blogr.ui.navigation.sticky' => false]);

        $html = view('blogr-comments::comments', [
            'postSlug' => 'test-post',
            'comments' => collect([]),
            'sort' => 'newest',
        ])->render();

        $this->assertStringContainsString('navSticky: false', $html);
    }

    #[Test]
    public function alpine_component_has_required_methods(): void
    {
        $html = view('blogr-comments::comments', [
            'postSlug' => 'test-post',
            'comments' => collect([]),
            'sort' => 'newest',
        ])->render();

        $this->assertStringContainsString('computeScrollOffset()', $html);
        $this->assertStringContainsString('scrollToComment()', $html);
        $this->assertStringContainsString('permalink(commentId)', $html);
    }
}
