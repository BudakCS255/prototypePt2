<!DOCTYPE html>
<html>
<head>
    <title>Image Upload and Viewer</title>
</head>
<body>
    <h1>Upload Images</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
        <!-- Allow multiple file selection -->
        <label for="image">Choose image(s) to upload:</label>
        <input type="file" name="image[]" id="image" accept="image/*" multiple>
        <br>
        <label for="folder">Select a folder:</label>
        <select name="folder" id="folder">
            <option value="Case001">Case001</option>
            <option value="Case002">Case002</option>
            <option value="Case003">Case003</option>
        </select>
        <br>
        <input type="submit" value="Upload">
    </form>

    <!-- The image viewing section -->
    <h1>Image Viewer</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
        <label for="view_folder">Select a folder to view images:</label>
        <select name="folder" id="view_folder">
            <option value="Case001">Case001</option>
            <option value="Case002">Case002</option>
            <option value="Case003">Case003</option>
        </select>
        <input type="submit" name="view_images" value="View Images">
        <!-- Add a button to trigger the download -->
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?folder=<?php echo $_GET['folder']; ?>&download=1" class="download-link">Download All Images as ZIP</a>
    </form>

    <?php
    // Function to sanitize the folder name input
    function sanitize_folder($folder) {
        // Implement appropriate sanitization for the folder input
        // For example, you might want to ensure that only valid folder names are processed
        return filter_var($folder, FILTER_SANITIZE_STRING);
    }

    // XOR encryption function
    function xor_encrypt($data, $key) {
        $keyLength = strlen($key);
        $encrypted = '';

        for ($i = 0; $i < strlen($data); $i++) {
            $encrypted .= $data[$i] ^ $key[$i % $keyLength];
        }

        return $encrypted;
    }

    // XOR decryption function (for consistency)
    function xor_decrypt($data, $key) {
        return xor_encrypt($data, $key);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if files were uploaded
        if (isset($_FILES["image"])) {
            $uploadedFiles = $_FILES["image"];

            // Database configuration
            $dbHost = 'localhost';
            $dbUser = 'afnan'; // Replace with your MySQL username
            $dbPass = 'john_wick_77'; // Replace with your MySQL password
            $dbName = 'mywebsite_images'; // Replace with your database name
            $imageColumnName = 'images'; // Replace with your BLOB column name

            // Create a database connection
            $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Loop through the uploaded files
            foreach ($uploadedFiles["error"] as $key => $error) {
                // Check for file upload errors
                if ($error == 0) {
                    // Get the selected folder and image data
                    $folder = $_POST["folder"];
                    $imageData = file_get_contents($uploadedFiles["tmp_name"][$key]);

                    // Define your encryption key
                    $encryptionKey = '123'; // Replace with your actual key

                    // Encrypt the image data
                    $encryptedImageData = xor_encrypt($imageData, $encryptionKey);

                    // Prepare and execute the database insertion
                    $stmt = $conn->prepare("INSERT INTO $folder ($imageColumnName) VALUES (?)");
                    $stmt->bind_param("b", $encryptedImageData); // Use "b" for binary data
                    $stmt->send_long_data(0, $encryptedImageData); // Send binary data separately
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $message = "Images uploaded successfully!";
                    } else {
                        $message = "Failed to upload images.";
                    }

                    // Close the statement
                    $stmt->close();
                } else {
                    $message = "File upload error: " . $error;
                }
            }

            // Close the database connection
            $conn->close();

            // Redirect back to index.php with the message
            header("Location: index.php?message=" . urlencode($message));
            exit();
        } else {
            $message = "No images uploaded.";
        }

        // Redirect back to index.php with the message
        header("Location: index.php?message=" . urlencode($message));
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['download']) && $_GET['download'] == 1 && isset($_GET['folder'])) {
        include 'download_images.php'; // Include the code to handle image download and ZIP creation
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['view_images'])) {
        // Your image_viewer.php logic here
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
            echo "<div class='image-container'>";
            while ($row = $result->fetch_assoc()) {
                $imageId = $row["id"];
                $encryptedImageData = $row["images"];

                // Define your decryption key (must be the same as the encryption key)
                $encryptionKey = '123'; // Replace with your actual key

                // Decrypt the image data
                $decryptedImageData = xor_decrypt($encryptedImageData, $encryptionKey);

                $base64Image = base64_encode($decryptedImageData);
                echo "<div class='image-item'>";
                echo "<h2>Image $imageId</h2>";
                echo "<img src='data:image/jpeg;base64,$base64Image' alt='Image $imageId'>";
                echo "</div>";
            }
            echo "</div>";

            // ...
        } else {
            echo "No images found in $selectedFolder.";
        }

        $conn->close();
    }
    ?>

    <!-- Feedback area for displaying messages -->
    <div id="upload-feedback">
        <?php
        if (isset($_GET['message'])) {
            echo '<p>' . htmlspecialchars($_GET['message']) . '</p>';
        }
        ?>
    </div>
</body>
</html>
