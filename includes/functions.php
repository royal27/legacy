<?php
// Helper functions will be added here

function parse_bbcode($text) {
    // Escape HTML to prevent XSS
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // [url=https://example.com]Link Text[/url]
    $text = preg_replace(
        '/\[url=(.*?)\](.*?)\[\/url\]/i',
        '<a href="$1" target="_blank" rel="noopener noreferrer">$2</a>',
        $text
    );

    // [url]https://example.com[/url]
    $text = preg_replace(
        '/\[url\](.*?)\[\/url\]/i',
        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
        $text
    );

    return $text;
}
?>
