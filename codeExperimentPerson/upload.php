<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && isset($_POST['email'])) {
    $email = preg_replace("/[^a-zA-Z0-9@.]/", "", $_POST['email']); // sanitize
    $file = $_FILES['file'];

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir);
    }

    // Create a unique filename
    $timestamp = time();
    $originalName = basename($file['name']);
    $safeEmail = str_replace('@', '_at_', $email);
    $savedName = 'upload_' . $safeEmail . '_' . $timestamp . '_' . $originalName;
    $targetPath = $uploadDir . $savedName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Append info to CSV
        $csvFile = 'uploads.csv';
        $row = [$email, $originalName, $savedName, date('Y-m-d H:i:s')];

        $fp = fopen($csvFile, 'a');
        if ($fp) {
            fputcsv($fp, $row);
            fclose($fp);
        }

        echo "Saved as: " . $savedName;
    } else {
        echo "❌ Error saving file.";
    }
} else {
    echo "❌ Invalid upload.";
}
?>
