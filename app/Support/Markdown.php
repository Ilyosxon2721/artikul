<?php

declare(strict_types=1);

namespace App\Support;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Mews\Purifier\Facades\Purifier;

/**
 * Renders untrusted user markdown to safe HTML:
 *  - GitHub-flavoured CommonMark (tables, strikethrough, autolinks)
 *  - HTML purified with HTMLPurifier (no JS, no inline events, narrow tag whitelist)
 *  - Result is cached statically per-process to avoid double rendering on Livewire re-render
 */
final class Markdown
{
    /** @var array<string, string> */
    private static array $cache = [];

    public static function render(?string $source): string
    {
        if ($source === null || trim($source) === '') {
            return '';
        }

        $key = hash('xxh3', $source);
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $env = new Environment([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
        $env->addExtension(new CommonMarkCoreExtension);
        $env->addExtension(new GithubFlavoredMarkdownExtension);
        $env->addExtension(new AutolinkExtension);

        $converter = new MarkdownConverter($env);
        $html = (string) $converter->convert($source);

        $clean = (string) Purifier::clean($html, [
            'HTML.Allowed' => 'p,br,strong,em,u,s,code,pre,blockquote,ul,ol,li,a[href|title|rel],h1,h2,h3,h4,h5,h6,hr,table,thead,tbody,tr,th,td',
            'HTML.TargetBlank' => true,
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => true,
            'CSS.AllowedProperties' => '',
        ]);

        return self::$cache[$key] = $clean;
    }
}
