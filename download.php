
<?php
// Simulate a check for user type
$userType = 'B'; // In a real application, this would come from a user session or similar

// Check if user is of type B
if ($userType != 'B') {
    echo "Access denied. You do not have permission to view or download images.";
    exit;
}

// Handle image viewing and downloading logic here
?>

<!DOCTYPE html>
<html>
<head>
    <title>View and Download Images - Type B Users</title>
</head>
<body>
    <h1>Image Viewer and Downloader</h1>
    <!-- Image viewing and downloading interface here -->
</body>
</html>
