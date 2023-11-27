
<?php
// Simulate a check for user type
$userType = 'C'; // In a real application, this would come from a user session or similar

// Check if user is of type C
if ($userType != 'C') {
    echo "Access denied. You do not have permission to access this page.";
    exit;
}

// Combined logic for uploading and downloading images
?>

<!DOCTYPE html>
<html>
<head>
    <title>Full Access - Type C Users</title>
</head>
<body>
    <h1>Image Upload and Download</h1>
    <!-- Image upload interface -->
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="image">Choose image(s) to upload:</label>
        <input type="file" name="image[]" id="image" accept="image/*" multiple><br><br>
        <input type="submit" value="Upload">
    </form>

    <!-- Image viewing and downloading interface -->
    <!-- Implementation similar to download.php -->
</body>
</html>
