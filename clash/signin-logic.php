<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$email) {
        $_SESSION['login'] = "Username or email required";
    } elseif (!$passwd) {
        $_SESSION['login'] = "Password required";
    } else {
        $fetch_user_query = "SELECT * FROM users WHERE email='$email'";
        $fetch_user_result = mysqli_query($connection, $fetch_user_query);

        if (mysqli_num_rows($fetch_user_result) == 1) {
            $user_record = mysqli_fetch_assoc($fetch_user_result);
            $db_pass = $user_record['passwd'];
            $is_verified = $user_record['verified'];

            if (password_verify($passwd, $db_pass)) {
                if ($is_verified) {
                    // User is verified, proceed to the dashboard or desired page
                    $_SESSION['user_id'] = $user_record['id'];
                    header('location: ' . ROOT_URL . 'index.php');
                    die();
                } else {
                    // User is not verified, redirect to the verification page
                    header('location: ' . ROOT_URL . 'verify.php?email=' . urlencode($email));
                    die();
                }
            } else {
                $_SESSION['login'] = "Wrong Email or Password";
            }
        } else {
            $_SESSION['login'] = "User not found";
        }
    }

    if (isset($_SESSION['login'])) {
        $_SESSION['login-data'] = $_POST;
        header('location: ' . ROOT_URL . 'signin.php');
        die();
    }
} else {
    header('location: ' . ROOT_URL . 'signin.php');
    die();
}
