
<?php
// Simulate a check for user type
$userType = 'A'; // In a real application, this would come from a user session or similar

// Check if user is of type A
if ($userType != 'A') {
    echo "Access denied. You do not have permission to upload images.";
    exit;
}

// Handle image upload logic here
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Images - Type A Users</title>
</head>
<body>
    <h1>Image Upload</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="image">Choose image(s) to upload:</label>
        <input type="file" name="image[]" id="image" accept="image/*" multiple><br><br>
        <input type="submit" value="Upload">
    </form>
</body>
</html>
