<?php
require 'config/constants.php';

$username_email = $_SESSION['signin-data']['username_email'] ?? null;
$password = $_SESSION['signin-data']['password'] ?? null;

unset($_SESSION['signin-data']);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Signin Admin</title>
    <link rel="stylesheet" href="../css/style-.css" />
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
          <h1 class="auth-title">Login Admin</h1>
        </div>
        <?php if (isset($_SESSION['signin'])) : ?>
        <div class="message__error">
            <p>
                <?= $_SESSION['signin'];
                unset($_SESSION['signin']);
                ?>
            </p>
        </div>
        <?php endif ?>
        <form class="auth-form" action="<?= ROOT_URL ?>admin/signin-logic.php" method="POST">
          <div class="input-group">
            <label class="input-label" for="email">Email</label>
            <input class="input-field" type="text" id="email" name="username_email" value="<?= $username_email ?>" placeholder="your email or username" required/>
          </div>
          <div class="input-group">
            <label class="input-label" for="password">Password</label>
            <input class="input-field" type="password" id="password" name="password" value="<?= $password ?>" placeholder="password" required />
          </div>
          <button class="btn btn-primary" type="submit" name="submit">Signin</button>
        </form>
      </div>
    </div>
  </body>
</html>
