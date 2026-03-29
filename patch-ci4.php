<?php
// Path to the problematic CodeIgniter file
$file = './system/I18n/Time.php';

if (!file_exists($file)) {
    die("Error: CI4 Time.php not found. Run 'composer install' first.\n");
}

$content = file_get_contents($file);

/**
 * We are updating the signature to match PHP 8.5 requirements:
 * 1. Change 'int $timestamp' to 'int|float $timestamp'
 * 2. Add ': static' return type
 */
$oldSignature = 'public static function createFromTimestamp(int $timestamp, $timezone = null, ?string $locale = null)';
$newSignature = 'public static function createFromTimestamp(int|float $timestamp, $timezone = null, ?string $locale = null): static';

if (strpos($content, $oldSignature) !== false) {
    $updatedContent = str_replace($oldSignature, $newSignature, $content);
    file_put_contents($file, $updatedContent);
    echo "Successfully patched CI4 Time.php for PHP 8.5 compatibility.\n";
} else {
    echo "ℹCI4 Time.php is already patched or the signature has changed.\n";
}