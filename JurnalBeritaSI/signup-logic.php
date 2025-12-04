<?php
require 'config/database.php';

// get signup form data if signup button was clicked
if (isset($_POST['submit'])) {
    $error = false; // Inisialisasi variabel error
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $avatar = $_FILES['avatar'];

    // Validate input values
    if (!$username) {
        $_SESSION['signup'] = "Please enter your Username";
        $error = true;
    } elseif (!$email) {
        $_SESSION['signup'] = "Please enter your a valid email";
        $error = true;
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['signup'] = "Password should be 8+ characters";
        $error = true;
    } elseif (!$avatar['name']) {
        $_SESSION['signup'] = "Please add avatar";
        $error = true;
    } else {
        // check if password don't match
        if($createpassword !== $confirmpassword) {
            $_SESSION['signup'] = "Password do not match";
            $error = true;
        } else {
            // hash password
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);
            
            // check if username or email already exist in database
            $user_check_query = "SELECT * FROM users where username='$username' OR email='$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);
            if (mysqli_num_rows($user_check_result) > 0) {
                $_SESSION['signup'] = "Username or Email already exist";
                $error = true;
            } else {
                // WORK ON AVATAR (Hanya jika tidak ada error sebelumnya)
                if (!$error) {
                    // rename avatar
                    $time = time(); // make each image name unique using current timestamp
                    $avatar_name = $time . $avatar['name'];
                    $avatar_tmp_name = $avatar['tmp_name'];
                    $avatar_destination_path = 'images/' . $avatar_name;

                    // make sure file is an image
                    $allowed_files = ['png', 'jpg', 'jpeg'];
                    $extention = explode('.', $avatar_name);
                    $extention = end($extention);
                    if (in_array($extention, $allowed_files)) {
                        // make sure image is not too large 1mb+)
                        if ($avatar['size'] < 1000000) {
                            // upload avatar
                            move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
                        } else {
                            $_SESSION['signup'] = "File size too big. Should be less than 1mb";
                            $error = true;
                        }
                    } else {
                        $_SESSION['signup'] = "File should be png, jpg, or jpeg";
                        $error = true;
                    }
                }
            }
        }
    }
    
    // Tangani semua redirect berdasarkan flag $error
    if ($error) {
        $_SESSION['signup-data'] = $_POST;
        header('location: ' . ROOT_URL . 'signup.php');
        die();
    } else {
        // insert new user into users table
        $insert_user_query = "INSERT INTO users SET username='$username', email='$email', password='$hashed_password', avatar='$avatar_name', is_admin=0";
        $insert_user_result = mysqli_query($connection, $insert_user_query);

        if ($insert_user_result) {
            // redirect to login page with success message
            $_SESSION['signup-success'] = "Registration successful. Please log in";
            header('location: ' . ROOT_URL . 'signin.php');
            die();
        } else {
            $_SESSION['signup'] = "Failed to register user: " . mysqli_error($connection); // Tambahkan pesan error MySQL
            $_SESSION['signup-data'] = $_POST; // Pass data kembali ke form
            header('location: ' . ROOT_URL . 'signup.php');
            die();
        }
    }
} else {
    // if button wasn't clicked bounce back to signup page
    header('location: ' . ROOT_URL . 'signup.php');
    die();
}