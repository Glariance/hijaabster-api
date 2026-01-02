<?php
// Temporary diagnostic file - remove after fixing the issue
header('Content-Type: application/json');

$info = [
    'php_version' => PHP_VERSION,
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: 'Not set (using system default)',
    'sys_temp_dir' => sys_get_temp_dir(),
    'temp_dir_writable' => is_writable(sys_get_temp_dir()),
    'temp_dir_exists' => file_exists(sys_get_temp_dir()),
    'disk_free_space' => disk_free_space(sys_get_temp_dir()) ? number_format(disk_free_space(sys_get_temp_dir()) / 1024 / 1024, 2) . ' MB' : 'Unknown',
];

echo json_encode($info, JSON_PRETTY_PRINT);

