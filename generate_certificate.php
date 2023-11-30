<?php
// Include the Imagick library
require 'phpqrcode/qrlib.php'; // Include the QRcode library

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user-submitted data
    $full_name = $_POST["full_name"];
    $course = $_POST["course"];

    // Generate a QR code
    $qr_size = 8;
    $qr_code_url = 'http://127.0.0.1/admin/certificates/' . uniqid() . '.jpg'; // Replace with your actual URL
    $qr_code_path = __DIR__ . '/temp_qr.png'; // Specify the path for the QR code image
    QRcode::png($qr_code_url, $qr_code_path, QR_ECLEVEL_L, $qr_size);

    // Create a new Imagick image
    $image = new Imagick();
    $image->newImage(2480, 3540, '#000000', 'jpg');

    // Set the image resolution (DPI) - adjust as needed
    $image->setImageResolution(300, 300);
    $image->setImageFormat("jpg"); // Set the format

    // Load the certificate image
    $background = new Imagick();
    $background->readImage(__DIR__ . '/certificate.jpg'); // Adjust the path

    // Copy the certificate background to the new image
    $image->compositeImage($background, Imagick::COMPOSITE_DEFAULT, 0, 0);

    // Create an ImagickDraw object for adding text
    $draw = new ImagickDraw();
    $fillColor = new ImagickPixel('black');
    $draw->setFillColor($fillColor); // Text color

    $draw->setFont(__DIR__ . '/Vazir.ttf'); // Path to a Persian font

    // Set font size and gravity for text
    $draw->setFontSize(70);
    $draw->setGravity(Imagick::GRAVITY_CENTER);

    // Calculate the position for text
    $x_full_name = -500;
    $y_full_name = -850;
    $x_course = -800;
    $y_course = -320;

    // Add Persian text to the image
    $image->annotateImage($draw, $x_full_name, $y_full_name, 0, "$full_name"); // Add your text
    $image->annotateImage($draw, $x_course, $y_course, 0, "$course");

    // Load the QR code image
    $qr_image = new Imagick($qr_code_path);

    // Calculate the position for the QR code
    $x_qr = 45;
    $y_qr = 3080;

    // Overlay the QR code onto the certificate image
    $image->compositeImage($qr_image, Imagick::COMPOSITE_OVER, $x_qr, $y_qr);

    // Save the modified image as a new .jpg file
    $newCertificateImagePath = 'certificates/' . uniqid() . '.jpg';
    $image->writeImage(__DIR__ . '/' . $newCertificateImagePath); // Adjust the path

    // Display the generated image in the browser
    header('Content-Type: image/jpeg');
    echo $image;

    // Free up memory
    $background->clear();
    $image->clear();
    $qr_image->clear();
}
?>
