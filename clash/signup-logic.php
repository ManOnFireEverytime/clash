<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

require 'config/database.php';
require 'vendor/autoload.php'; // Ensure you have included PHPMailer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $passwd = $_POST['password'];
    $verificationCode = mt_rand(1000, 9999);

    // Validation
    if (!$username) {
        echo json_encode(["success" => false, "message" => "Please enter a user name."]);
        exit();
    }
    if (!$email) {
        echo json_encode(["success" => false, "message" => "Please enter a valid email."]);
        exit();
    }
    if (!str_ends_with($email, '@thevaultldn.com')) {
        echo json_encode(["success" => false, "message" => "Email must end with @thevaultldn.com."]);
        exit();
    }
    if (strlen($passwd) < 8) {
        echo json_encode(["success" => false, "message" => "Password should have 8 or more characters."]);
        exit();
    }

    // Check if the email already exists
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already registered."]);
        exit();
    } else {
        // Insert new user
        $hashed_pword = password_hash($passwd, PASSWORD_BCRYPT);
        $insert_user_query = "INSERT INTO admin (username, email, passwd, verification) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($insert_user_query);
        $stmt->bind_param("ssss", $username, $email, $hashed_pword, $verificationCode);

        if ($stmt->execute()) {
            // Send verification email using PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'mofe@axelcyber.com'; // Replace with your SMTP username
            $mail->Password = 'Mofecyber#001'; // Replace with your SMTP password
            $mail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' depending on your server configuration
            $mail->Port = 465; // Adjust the port if needed

            $mail->setFrom('mofe@axelcyber.com', 'Admin'); // Replace with your email and name
            $mail->addAddress($email, $fname . ' ' . $lname);
            $mail->Subject = 'Email Verification';
            $mail->Body = "Your verification code is: $verificationCode";

            if ($mail->send()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Registration successful. Please check your email for the verification code.",
                    "redirect" => "http://admin.thevaultldn.com/verify?email=" . urlencode($email) // Include the email in the URL
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Error sending verification email. Please try again."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Failed to register."]);
        }
    }

    $stmt->close();
}
$connection->close();
