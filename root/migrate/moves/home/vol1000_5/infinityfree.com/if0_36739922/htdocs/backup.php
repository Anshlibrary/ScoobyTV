<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: Babualogin.php');
    exit;
}

// Define the path to the folder you want to zip
$rootPath = realpath('../htdocs');
if ($rootPath === false) {
    die('Invalid root path');
}

// Get current date and time
$dateTime = date('Y-m-d_H:i:s');

// Create the backup directory if it doesn't exist
$backupDir = __DIR__ . '/backup';
if (!is_dir($backupDir)) {
    if (!mkdir($backupDir, 0755, true)) {
        die('Failed to create backup directory');
    }
}

// Create a filename with the current date and time
$zipFileName = 'backup_htdocs_' . $dateTime . '.zip';
$zipFilePath = $backupDir . '/' . $zipFileName;

// Initialize archive object
$zip = new ZipArchive();
if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    // Create recursive directory iterator
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            if (!$zip->addFile($filePath, $relativePath)) {
                die('Failed to add file: ' . $filePath);
            }
        }
    }

    // Close the zip archive
    $zip->close();
    echo 'Backup created successfully: ' . $zipFilePath;
    echo '<script>
    setTimeout(function() {
        window.location.href = "admin/services.php";
    }, 5000);
</script>';
} else {
    die('Failed to create zip file');
}
?>
