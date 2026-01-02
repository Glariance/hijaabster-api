<!DOCTYPE html>
<html>
<head>
    <title>PHP Upload Configuration Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .error { background: #fee; border: 1px solid #fcc; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #efe; border: 1px solid #cfc; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .warning { background: #ffe; border: 1px solid #ffc; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .info { background: #eef; border: 1px solid #ccf; padding: 15px; margin: 10px 0; border-radius: 5px; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h1>PHP Upload Configuration Check</h1>
    
    <?php
    $issues = [];
    $warnings = [];
    
    // Check upload_max_filesize
    $uploadMax = ini_get('upload_max_filesize');
    $uploadMaxBytes = return_bytes($uploadMax);
    if ($uploadMaxBytes < 5242880) { // 5MB
        $issues[] = "upload_max_filesize is too small: {$uploadMax} (should be at least 10M)";
    }
    
    // Check post_max_size
    $postMax = ini_get('post_max_size');
    $postMaxBytes = return_bytes($postMax);
    if ($postMaxBytes < $uploadMaxBytes * 1.2) {
        $issues[] = "post_max_size ({$postMax}) should be larger than upload_max_filesize ({$uploadMax})";
    }
    
    // Check temp directory
    $tempDir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
    if (!file_exists($tempDir)) {
        $issues[] = "Temp directory does not exist: {$tempDir}";
    } elseif (!is_writable($tempDir)) {
        $issues[] = "Temp directory is not writable: {$tempDir}";
    } else {
        $freeSpace = disk_free_space($tempDir);
        if ($freeSpace < 10485760) { // 10MB
            $warnings[] = "Low disk space in temp directory: " . number_format($freeSpace / 1024 / 1024, 2) . " MB";
        }
    }
    
    function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }
    ?>
    
    <h2>Current Settings</h2>
    <div class="info">
        <strong>upload_max_filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?><br>
        <strong>post_max_size:</strong> <?php echo ini_get('post_max_size'); ?><br>
        <strong>upload_tmp_dir:</strong> <?php echo ini_get('upload_tmp_dir') ?: 'Not set (using: ' . sys_get_temp_dir() . ')'; ?><br>
        <strong>Temp directory writable:</strong> <?php echo is_writable($tempDir) ? 'Yes' : 'No'; ?><br>
        <strong>Free space:</strong> <?php echo number_format(disk_free_space($tempDir) / 1024 / 1024, 2); ?> MB
    </div>
    
    <?php if (!empty($issues)): ?>
        <h2>Issues Found</h2>
        <?php foreach ($issues as $issue): ?>
            <div class="error"><?php echo htmlspecialchars($issue); ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (!empty($warnings)): ?>
        <h2>Warnings</h2>
        <?php foreach ($warnings as $warning): ?>
            <div class="warning"><?php echo htmlspecialchars($warning); ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (empty($issues) && empty($warnings)): ?>
        <div class="success">
            <strong>✓ All checks passed!</strong> Your PHP configuration should allow file uploads.
        </div>
    <?php endif; ?>
    
    <h2>How to Fix</h2>
    <div class="info">
        <p><strong>1. Find your php.ini file:</strong></p>
        <code><?php echo php_ini_loaded_file(); ?></code>
        
        <p><strong>2. Edit php.ini and set these values:</strong></p>
        <pre>
upload_max_filesize = 10M
post_max_size = 12M
upload_tmp_dir = C:\Users\FA\AppData\Local\Temp
        </pre>
        
        <p><strong>3. Restart your PHP server</strong> (Laravel dev server, XAMPP, etc.)</p>
        
        <p><strong>4. If using XAMPP/WAMP:</strong> Make sure you're editing the correct php.ini (the one used by your web server, not CLI)</p>
    </div>
    
    <h2>Test Upload</h2>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="file" name="test_file" accept="image/*">
        <button type="submit">Test Upload</button>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
        echo '<h3>Upload Test Result</h3>';
        if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            echo '<div class="success">✓ File uploaded successfully! Size: ' . $_FILES['test_file']['size'] . ' bytes</div>';
        } else {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'PHP extension stopped the upload',
            ];
            $errorMsg = $errors[$_FILES['test_file']['error']] ?? 'Unknown error';
            echo '<div class="error">✗ Upload failed: ' . $errorMsg . ' (Error code: ' . $_FILES['test_file']['error'] . ')</div>';
        }
    }
    ?>
</body>
</html>

