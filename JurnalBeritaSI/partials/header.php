<?php
require 'config/database.php';

// fetch current user from database
if (isset($_SESSION['user-id'])) {
    $id = filter_var($_SESSION['user-id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT avatar, is_admin FROM users WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $avatar = mysqli_fetch_assoc($result);
} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Artikel Berita - Sistem Informasi</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
    <!-- Unicon CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.2.0/css/line.css">
</head>
<body>

  <nav>
    <div class="container nav_container">
      <div class="nav_brand">
        <a href="<?= ROOT_URL ?>index.php" class="nav_logo"><img src="<?= ROOT_URL ?>images/Logo.png" alt="Logo" class="logo-img"></a>
        <a href="<?= ROOT_URL ?>index.php" class="nav_nama_logo">Sistem Informasi</a>
      </div>
        <ul class="nav_items">
            <li><a href="<?= ROOT_URL ?>sistem-informasi.php">Berita Sistem Informasi</a></li>
            <li><a href="<?= ROOT_URL ?>berita-terbaru.php">Berita Terbaru</a></li>
            <li><a href="<?= ROOT_URL ?>berita-populer.php">Berita Populer</a></li>
            <?php if(isset($_SESSION['user-id'])) : ?>
            <li class="nav_profile">
              <div class="avatar"> 
              <img src="<?= ROOT_URL . 'images/' . $avatar['avatar'] ?>">
              </div>
              <ul>
<?php if (isset($avatar['is_admin']) && $avatar['is_admin'] == 1) : ?>
                     <li><a href="<?= ROOT_URL ?>admin/index.php">Dashboard</a></li>
<?php endif; ?>
                     <li><a href="<?= ROOT_URL ?>logout.php">Logout</a></li>
                 </ul>
          </li>
            <?php else : ?>
            <li><a href="<?= ROOT_URL ?>signin.php">Signin</a></li>
            <?php endif; ?>
        </ul>

        <button id="open_nav-btn"><i class="uil uil-bars"></i></button>
        <button id="close_nav-btn"><i class="uil uil-multiply"></i></button>
    </div>
</nav>
<!-- =========== END OF NAV =========== -->