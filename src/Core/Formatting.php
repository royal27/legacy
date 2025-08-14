<?php

namespace App\Core;

class Formatting
{
    /**
     * A simple BBCode to HTML parser.
     *
     * @param string $text
     * @return string
     */
    public static function bbcode_to_html($text)
    {
        // First, escape HTML to prevent XSS
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // Define BBCode search and replace patterns
        $patterns = [
            '/\[b\](.*?)\[\/b\]/s' => '<strong>$1</strong>',
            '/\[i\](.*?)\[\/i\]/s' => '<em>$1</em>',
            '/\[u\](.*?)\[\/u\]/s' => '<span style="text-decoration: underline;">$1</span>',
            '/\[url\](.*?)\[\/url\]/s' => '<a href="$1" target="_blank" rel="nofollow">$1</a>',
            '/\[url=(.*?)\](.*?)\[\/url\]/s' => '<a href="$1" target="_blank" rel="nofollow">$2</a>',
            '/\[img\](.*?)\[\/img\]/s' => '<img src="$1" alt="Image" style="max-width:100%;">',
            '/\[quote\](.*?)\[\/quote\]/s' => '<blockquote>$1</blockquote>',
            '/\[code\](.*?)\[\/code\]/s' => '<pre><code>$1</code></pre>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        // Handle new lines
        $text = nl2br($text);

        return $text;
    }

    /**
     * A simple emoji parser.
     *
     * @param string $text
     * @return string
     */
    public static function parse_emojis($text)
    {
        $emojis = [
            ':)' => '🙂',
            ':(' => '🙁',
            ';)' => '😉',
            ':D' => '😃',
            ':P' => '😛',
            ':O' => '😮',
            '<3' => '❤️',
        ];

        return str_replace(array_keys($emojis), array_values($emojis), $text);
    }

    /**
     * A combined formatter for posts.
     *
     * @param string $text
     * @return string
     */
    public static function format_post($text)
    {
        $text = self::bbcode_to_html($text);
        $text = self::parse_emojis($text);
        return $text;
    }
}
