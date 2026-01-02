<?php
/**
 * PHP Upload Configuration Fixer
 * Run this script from command line: php fix-php-upload.php
 */

echo "PHP Upload Configuration Fixer\n";
echo "===============================\n\n";

$phpIniPath = php_ini_loaded_file();
$customTempDir = __DIR__ . '/storage/app/temp_uploads';

echo "Current PHP Configuration:\n";
echo "- php.ini location: " . ($phpIniPath ?: 'Not found') . "\n";
echo "- upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'Not set (using: ' . sys_get_temp_dir() . ')') . "\n";
echo "- upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "- post_max_size: " . ini_get('post_max_size') . "\n\n";

// Create custom temp directory
if (!file_exists($customTempDir)) {
    if (mkdir($customTempDir, 0755, true)) {
        echo "✓ Created custom temp directory: $customTempDir\n";
    } else {
        echo "✗ Failed to create custom temp directory: $customTempDir\n";
        exit(1);
    }
} else {
    echo "✓ Custom temp directory exists: $customTempDir\n";
}

if (!is_writable($customTempDir)) {
    echo "✗ Custom temp directory is not writable!\n";
    echo "  Please run: chmod 755 $customTempDir\n";
    exit(1);
} else {
    echo "✓ Custom temp directory is writable\n";
}

echo "\n";

if (!$phpIniPath) {
    echo "ERROR: Could not find php.ini file!\n";
    echo "Please manually edit your php.ini file and add:\n\n";
    echo "upload_tmp_dir = \"$customTempDir\"\n";
    echo "upload_max_filesize = 10M\n";
    echo "post_max_size = 12M\n\n";
    exit(1);
}

echo "To fix the upload issue, edit: $phpIniPath\n\n";
echo "Add or update these lines:\n\n";
echo "upload_tmp_dir = \"$customTempDir\"\n";
echo "upload_max_filesize = 10M\n";
echo "post_max_size = 12M\n\n";

echo "After editing, RESTART your PHP server!\n";
echo "\n";
echo "For Laravel dev server: Stop (Ctrl+C) and run 'php artisan serve' again\n";
echo "For XAMPP/WAMP: Restart Apache service\n";

