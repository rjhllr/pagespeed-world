<?php

namespace App\Helpers;

class BlogContentProcessor
{
    /**
     * Process blog content: generate TOC, fix tables, prepare for display
     */
    public static function process(string $html): array
    {
        // Generate Table of Contents
        $toc = self::generateToc($html);

        // Add IDs to headings for TOC links
        $html = self::addHeadingIds($html);

        // Convert markdown tables to HTML tables
        $html = self::convertMarkdownTables($html);

        // Style tables
        $html = self::styleTables($html);

        // Style inline code
        $html = self::styleInlineCode($html);

        return [
            'html' => $html,
            'toc' => $toc,
        ];
    }

    /**
     * Generate Table of Contents from h2 and h3 headings
     */
    protected static function generateToc(string $html): array
    {
        $toc = [];

        // Match h2 and h3 headings with their IDs (from frontmatter {#id})
        preg_match_all('/<h([23])(?:\s+id="([^"]*)")?[^>]*>(.*?)<\/h\1>/i', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $level = (int) $match[1];
            $content = strip_tags($match[3]);

            // Extract custom ID from {#id} syntax
            $customId = null;
            if (preg_match('/\{#([a-z0-9-]+)\}/', $content, $idMatch)) {
                $customId = $idMatch[1];
                // Remove the {#id} syntax from the title
                $content = trim(preg_replace('/\s*\{#[a-z0-9-]+\}\s*/', '', $content));
            }

            $id = !empty($match[2]) ? $match[2] : ($customId ?? self::generateSlug($content));
            $title = $content;

            $toc[] = [
                'level' => $level,
                'id' => $id,
                'title' => $title,
            ];
        }

        return $toc;
    }

    /**
     * Add IDs to headings that don't have them
     */
    protected static function addHeadingIds(string $html): string
    {
        return preg_replace_callback('/<h([23])(?:\s+id="([^"]*)")?([^>]*)>(.*?)<\/h\1>/i', function ($matches) {
            $level = $matches[1];
            $existingId = $matches[2] ?? '';
            $attributes = $matches[3];
            $content = $matches[4];

            // Extract custom ID from {#id} syntax in content
            $customId = null;
            $cleanContent = strip_tags($content);
            if (preg_match('/\{#([a-z0-9-]+)\}/', $cleanContent, $idMatch)) {
                $customId = $idMatch[1];
                // Remove the {#id} syntax from the content
                $content = preg_replace('/\s*\{#[a-z0-9-]+\}\s*/', '', $content);
            }

            if (empty($existingId)) {
                $existingId = $customId ?? self::generateSlug(strip_tags($content));
            }

            return sprintf(
                '<h%s id="%s"%s>%s</h%s>',
                $level,
                $existingId,
                $attributes,
                $content,
                $level
            );
        }, $html);
    }

    /**
     * Convert markdown tables to HTML tables
     */
    protected static function convertMarkdownTables(string $html): string
    {
        // Match markdown tables wrapped in <p> tags or standalone
        // Pattern: header row | separator row | data rows
        $pattern = '/<p>\s*\|(.+?)\|\s*\n\s*\|[-|\s:]+\|\s*\n((?:\s*\|.+?\|\s*\n?)+)\s*<\/p>/s';

        return preg_replace_callback($pattern, function ($matches) {
            $headerRow = trim($matches[1]);
            $dataRows = trim($matches[2]);

            // Parse header cells
            $headers = array_map('trim', explode('|', $headerRow));

            // Parse data rows
            $rows = array_filter(array_map('trim', explode("\n", $dataRows)));

            $tableHtml = "<table>\n<thead>\n<tr>\n";
            foreach ($headers as $header) {
                $tableHtml .= "<th>" . htmlspecialchars($header) . "</th>\n";
            }
            $tableHtml .= "</tr>\n</thead>\n<tbody>\n";

            foreach ($rows as $row) {
                // Remove leading/trailing pipes and split
                $row = trim($row, '| ');
                $cells = array_map('trim', explode('|', $row));

                $tableHtml .= "<tr>\n";
                foreach ($cells as $cell) {
                    $tableHtml .= "<td>" . htmlspecialchars($cell) . "</td>\n";
                }
                $tableHtml .= "</tr>\n";
            }

            $tableHtml .= "</tbody>\n</table>";

            return $tableHtml;
        }, $html);
    }

    /**
     * Style tables with Tailwind classes
     */
    protected static function styleTables(string $html): string
    {
        // Wrap tables in a responsive container
        $html = preg_replace(
            '/<table>/i',
            '<div class="overflow-x-auto my-6"><table class="min-w-full divide-y divide-slate-700 border border-slate-700 rounded-lg">',
            $html
        );

        $html = preg_replace(
            '/<\/table>/i',
            '</table></div>',
            $html
        );

        // Style table headers
        $html = preg_replace(
            '/<thead>/i',
            '<thead class="bg-slate-800">',
            $html
        );

        $html = preg_replace(
            '/<th>/i',
            '<th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">',
            $html
        );

        // Style table body
        $html = preg_replace(
            '/<tbody>/i',
            '<tbody class="bg-slate-900 divide-y divide-slate-800">',
            $html
        );

        $html = preg_replace(
            '/<td>/i',
            '<td class="px-4 py-3 text-sm">',
            $html
        );

        return $html;
    }

    /**
     * Style inline code elements with syntax highlighting
     */
    protected static function styleInlineCode(string $html): string
    {
        // Process inline <code> tags (not inside <pre>)
        // First, temporarily replace <pre><code> blocks to protect them
        $preBlocks = [];
        $html = preg_replace_callback('/<pre[^>]*>.*?<\/pre>/s', function ($match) use (&$preBlocks) {
            $placeholder = '<!--PRE_BLOCK_' . count($preBlocks) . '-->';
            $preBlocks[$placeholder] = $match[0];
            return $placeholder;
        }, $html);

        // Now process inline <code> tags
        $html = preg_replace_callback('/<code>([^<]*)<\/code>/', function ($match) {
            $content = $match[1];

            // Fix escaped quotes from PHP string storage
            $content = str_replace('\\"', '"', $content);
            $content = str_replace("\\'", "'", $content);

            // Apply syntax highlighting
            $content = self::highlightInlineCode($content);

            return '<code class="inline-code">' . $content . '</code>';
        }, $html);

        // Restore <pre> blocks
        foreach ($preBlocks as $placeholder => $block) {
            $html = str_replace($placeholder, $block, $html);
        }

        return $html;
    }

    /**
     * Apply syntax highlighting to inline code content
     */
    protected static function highlightInlineCode(string $code): string
    {
        // Highlight HTML tags: <tagname ...> or </tagname>
        // Match opening tags with attributes
        $code = preg_replace_callback(
            '/(&lt;\/?)([a-zA-Z][a-zA-Z0-9-]*)([^&]*?)(&gt;)/',
            function ($m) {
                $bracket1 = '<span class="hl-punct">' . $m[1] . '</span>';
                $tagName = '<span class="hl-tag">' . $m[2] . '</span>';
                $attrs = self::highlightAttributes($m[3]);
                $bracket2 = '<span class="hl-punct">' . $m[4] . '</span>';
                return $bracket1 . $tagName . $attrs . $bracket2;
            },
            $code
        );

        // Highlight standalone attributes (not inside tags): attr="value"
        // Only if no tags were found
        if (strpos($code, 'hl-tag') === false) {
            $code = self::highlightAttributes($code);
        }

        return $code;
    }

    /**
     * Highlight attribute name="value" patterns
     */
    protected static function highlightAttributes(string $code): string
    {
        // Match attribute="value" or attribute='value'
        return preg_replace_callback(
            '/([a-zA-Z][a-zA-Z0-9_-]*)(\s*=\s*)(["\'])([^"\']*)\3/',
            function ($m) {
                $attrName = '<span class="hl-attr">' . $m[1] . '</span>';
                $equals = $m[2];
                $quote = $m[3];
                $value = '<span class="hl-string">' . $quote . $m[4] . $quote . '</span>';
                return $attrName . $equals . $value;
            },
            $code
        );
    }

    /**
     * Generate a slug from text
     */
    protected static function generateSlug(string $text): string
    {
        $slug = strtolower($text);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}
