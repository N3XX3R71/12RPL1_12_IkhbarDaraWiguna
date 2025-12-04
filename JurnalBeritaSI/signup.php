<?php 
require 'config/constants.php';

// get back form data if there was a registration error
$username = $_SESSION['signup-data']['username'] ?? null;
$email = $_SESSION['signup-data']['email'] ?? null;
$createpassword = $_SESSION['signup-data']['createpassword'] ?? null;
$confirmpassword = $_SESSION['signup-data']['confirmpassword'] ?? null;
// delete signup data session 
unset($_SESSION['signup-data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Signup</title>
    <link rel="stylesheet" href="css/style-.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI,
          Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        background: #da0f3f !important;
      }
      .btn-primary {
        background: #da0f3f !important;
        box-shadow: 0 8px 20px -6px rgba(218, 15, 63, 0.45) !important;
      }
      .btn-primary:hover {
        background: #b00c30 !important;
      }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="<?= ROOT_URL ?>images/Logo-Telkom.png" alt="Logo Telkom" class="logo-telkom">
            </div>
            <div class="auth-header">
                <h1 class="auth-title">Signup</h1>
            </div>
            <?php if (isset($_SESSION['signup'])) : ?>
            <div class="message__error">
                <p>
                    <?= $_SESSION['signup'];
                    unset($_SESSION['signup']);
                    ?>
                </p>
            </div>
            <?php endif ?>
            <form class="auth-form" action="<?= ROOT_URL ?>signup-logic.php" enctype="multipart/form-data" method="POST">
                <div class="input-group">
                    <label class="input-label" for="username">Username</label>
                    <input class="input-field" type="text" id="username" name="username" value="<?= $username ?>" placeholder="your username" required />
                </div>
                <div class="input-group">
                    <label class="input-label" for="email">Email</label>
                    <input class="input-field" type="email" id="email" name="email" value="<?= $email ?>" placeholder="your email" required />
                </div>
                <div class="input-group">
                    <label class="input-label" for="password">Password</label>
                    <input class="input-field" type="password" id="password" name="createpassword" value="<?= $createpassword ?>" placeholder="create password" required />
                </div>
                <div class="input-group">
                    <label class="input-label" for="confirm-password">Confirm Password</label>
                    <input class="input-field" type="password" id="confirm-password" name="confirmpassword" value="<?= $confirmpassword ?>" placeholder="confirm password" required />
                </div>
                <div class="form_control input-group">
                <label for="avatar">User Avatar</label>
                <input type="file" name="avatar" id="avatar">
                </div>
                <button class="btn btn-primary" type="submit" name="submit">Signup</button>
            </form>
            <p class="footnote">Already have an account? <a class="link" href="<?= ROOT_URL ?>signin.php">Signin</a></p>
        </div>
    </div>
</body>
</html>