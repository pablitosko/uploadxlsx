<?php

$tipo = " application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit('POST request method required');
}

if (empty($_FILES)) {
    exit('$_FILES is empty - is file_uploads set to "Off" in php.ini?');
}


if ($_FILES["file1"]["error"] !== UPLOAD_ERR_OK) {

    switch ($_FILES["file1"]["error"]) {
        case UPLOAD_ERR_PARTIAL:
            exit('File only partially uploaded');
            break;
        case UPLOAD_ERR_NO_FILE:
            exit('No file was uploaded');
            break;
        case UPLOAD_ERR_EXTENSION:
            exit('File upload stopped by a PHP extension');
            break;
        case UPLOAD_ERR_FORM_SIZE:
            exit('File exceeds MAX_FILE_SIZE in the HTML form');
            break;
        case UPLOAD_ERR_INI_SIZE:
            exit('File exceeds upload_max_filesize in php.ini');
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            exit('Temporary folder not found');
            break;
        case UPLOAD_ERR_CANT_WRITE:
            exit('Failed to write file');
            break;
        default:
            exit('Unknown upload error');
            break;
    }
}

// Reject uploaded file larger than 1MB
if ($_FILES["file1"]["size"] > 1048576) {
    exit('File too large (max 1MB)');
}

// Use fileinfo to get the mime type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->file($_FILES["file1"]["tmp_name"]);

$mime_types = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
        
if ( ! in_array($_FILES["file1"]["type"], $mime_types)) {
    exit("Invalid file type");
}

// Replace any characters not \w- in the original filename
$pathinfo = pathinfo($_FILES["file1"]["name"]);

$base = $pathinfo["filename"];

$base = preg_replace("/[^\w-]/", "_", $base);

$filename = $base . "." . $pathinfo["extension"];

$destination = __DIR__ . "/uploads/" . $filename;

// Add a numeric suffix if the file already exists
$i = 1;

while (file_exists($destination)) {

    $filename = $base . "($i)." . $pathinfo["extension"];
    $destination = __DIR__ . "/uploads/" . $filename;

    $i++;
}

if ( ! move_uploaded_file($_FILES["file1"]["tmp_name"], $destination)) {

    exit("Can't move uploaded file");

}

echo "File uploaded successfully.";