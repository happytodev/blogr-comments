<?php

namespace Happytodev\BlogrComments\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class CommentRenderer
{
    protected MarkdownConverter $converter;

    protected bool $highlighterAvailable;

    public function __construct()
    {
        $environment = new Environment([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 10,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);

        $this->converter = new MarkdownConverter($environment);
        $this->highlighterAvailable = class_exists(\Highlight\Highlighter::class);
    }

    public function toHtml(string $markdown): string
    {
        $html = $this->converter->convert($markdown)->getContent();

        if ($this->highlighterAvailable) {
            $html = $this->highlightCodeBlocks($html);
        }

        return $html;
    }

    protected function highlightCodeBlocks(string $html): string
    {
        $highlighter = new \Highlight\Highlighter;
        $highlighter->setAutodetectLanguages(['php', 'js', 'html', 'css', 'bash', 'json', 'xml', 'yaml', 'twig', 'blade']);

        return preg_replace_callback(
            '/<pre><code class="language-(\w+)">(.*?)<\/code><\/pre>/s',
            function (array $matches) use ($highlighter) {
                $lang = $matches[1];
                $code = htmlspecialchars_decode($matches[2]);
                $code = rtrim($code, "\n");

                try {
                    $result = $highlighter->highlight($lang, $code);
                    $highlighted = $result->value;
                } catch (\DomainException) {
                    $highlighted = htmlspecialchars($code);
                }

                $badge = sprintf('<span class="blogr-code-lang">%s</span>', htmlspecialchars($lang));

                return sprintf(
                    '<pre><code class="language-%s hljs">%s</code>%s</pre>',
                    htmlspecialchars($lang),
                    $highlighted,
                    $badge
                );
            },
            $html
        );
    }
}
