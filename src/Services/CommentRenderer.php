<?php

namespace Happytodev\BlogrComments\Services;

class CommentRenderer
{
    public function toHtml(string $markdown): string
    {
        $html = htmlspecialchars($markdown, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $html = $this->renderBold($html);
        $html = $this->renderItalic($html);
        $html = $this->renderInlineCode($html);
        $html = $this->renderCodeBlocks($html);
        $html = $this->renderBlockquotes($html);
        $html = $this->renderLinks($html);
        $html = $this->renderParagraphs($html);

        return $html;
    }

    protected function renderBold(string $html): string
    {
        return preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
    }

    protected function renderItalic(string $html): string
    {
        return preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
    }

    protected function renderInlineCode(string $html): string
    {
        return preg_replace('/`(.+?)`/', '<code>$1</code>', $html);
    }

    protected function renderCodeBlocks(string $html): string
    {
        return preg_replace('/```(\w*)\n(.*?)```/s', '<pre><code>$2</code></pre>', $html);
    }

    protected function renderBlockquotes(string $html): string
    {
        return preg_replace('/^&gt;\s?(.*)$/m', '<blockquote>$1</blockquote>', $html);
    }

    protected function renderLinks(string $html): string
    {
        return preg_replace(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            '<a href="$2" rel="nofollow noopener" target="_blank">$1</a>',
            $html
        );
    }

    protected function renderParagraphs(string $html): string
    {
        $html = preg_replace('/<\/blockquote>\s*<blockquote>/', "\n", $html);
        $html = preg_replace('/<pre><code>.*?<\/code><\/pre>/s', "\n$0\n", $html);

        $paragraphs = preg_split('/\n{2,}/', $html);
        $result = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if (empty($paragraph)) {
                continue;
            }

            if (str_starts_with($paragraph, '<blockquote>') || str_starts_with($paragraph, '<pre>')) {
                $result[] = $paragraph;
            } else {
                $paragraph = str_replace("\n", '<br>', $paragraph);
                $result[] = '<p>' . $paragraph . '</p>';
            }
        }

        return implode("\n", $result);
    }
}
