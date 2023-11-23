
<?php
session_start();

// Check if the user is logged in and has the 'view/download' role
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'view/download') {
    die("Access denied");
}

if (isset($_GET['download']) && $_GET['download'] == 1 && isset($_GET['folder'])) {
    // Get the selected folder
    $selectedFolder = sanitize_folder($_GET['folder']);

    // Database configuration - Update with your actual database credentials
    $dbHost = 'localhost';
    $dbUser = 'afnan';
    $dbPass = 'john_wick_77';
    $dbName = 'mywebsite_images';

    // Create a database connection
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to retrieve encrypted image data from the selected folder table
    $sql = "SELECT id, images FROM $selectedFolder";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Create a temporary directory for storing images
        $tempDir = sys_get_temp_dir() . '/' . uniqid('images_') . '/';
        mkdir($tempDir);

        while ($row = $result->fetch_assoc()) {
            $imageId = $row["id"];
            $encryptedImageData = $row["images"];

            // Define your decryption key (must be the same as the encryption key)
            $encryptionKey = '123'; // Replace with your actual key

            // Decrypt the image data
            $decryptedImageData = xor_decrypt($encryptedImageData, $encryptionKey);

            // Save the decrypted image to the temporary directory
            $imageFileName = $tempDir . 'image_' . $imageId . '.jpg';
            file_put_contents($imageFileName, $decryptedImageData);
        }

        // Create a ZIP file containing all images
        $zipFileName = sys_get_temp_dir() . '/' . 'images.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tempDir));
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir));
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            // Send the ZIP file for download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="images.zip"');
            readfile($zipFileName);

            // Clean up temporary files and directory
            unlink($zipFileName);
            foreach (glob($tempDir . '*') as $file) {
                unlink($file);
            }
            rmdir($tempDir);
            exit();
        } else {
            echo "Failed to create the ZIP file.";
        }
    } else {
        echo "No images found in $selectedFolder.";
    }

    $conn->close();
}
?>
