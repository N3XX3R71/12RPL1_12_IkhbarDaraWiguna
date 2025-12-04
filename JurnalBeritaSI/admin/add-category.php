<?php
    require 'config/database.php';

    // fetch user from database
    $current_user_id = $_SESSION['user-id'];
    $query = "SELECT * FROM users WHERE id = $current_user_id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);
    $avatar = $user['avatar'];

    // get back form data if invalid
    $title = $_SESSION['add-category-data']['title'] ?? null;
    $description = $_SESSION['add-category-data']['description'] ?? null;

    unset($_SESSION['add-category-data']);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Category</title>
    <link rel="stylesheet" href="../css/style.css" />
    <!-- ICONSCOUT CDN -->
    <link
      rel="stylesheet"
      href="https://unicons.iconscout.com/release/v4.0.8/css/line.css"
    />
  </head>
  <body>

  <div class="sidebar">
      <div class="profile-section">
        <img src="<?= ROOT_URL ?>images/<?= $avatar ?>" alt="Profile" class="profile-img" />
        <div class="profile-name"><?php echo $user['username'] ?></div>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="add-post.php"
            ><i class="uil uil-pen"></i> <span>Add Post</span></a
          >
        </li>
        <li>
          <a href="index.php" 
            ><i class="uil uil-postcard"></i> <span>Manage Post</span></a>
        </li>
        <li>
          <a href="add-category.php" class="active"
            ><i class="uil uil-pen"></i> <span>Add Category</span></a>
        </li>
        <li>
          <a href="manage-categories.php"
            ><i class="uil uil-document-layout-left"></i>
            <span>Manage Category</span></a>
        </li>
        <li>
          <a href="add-user.php"
            ><i class="uil uil-pen"></i> <span>Add User</span></a>
        </li>
        <li>
          <a href="manage-users.php"
            ><i class="uil uil-users-alt"></i>
            <span>Manage Users</span></a>
        </li>
        <li>
          <a href="manage-comment.php"
            ><i class="uil uil-comment-alt-notes"></i>
            <span>Manage Comment</span></a>
        </li>
        <li>
          <a href="report-post.php"
            ><i class="uil uil-file-alt"></i>
            <span>Report Post</span></a>
        </li>
        <li>
          <a href="<?= ROOT_URL ?>logout.php"><i class="uil uil-signout"></i> <span>Logout</span></a>
        </li>
      </ul>
    </div>

    <div class="center-wrapper">
      <form class="form-container" action="<?= ROOT_URL ?>admin/add-category-logic.php" method="POST">
        <h2>Add Category</h2>
        <?php if(isset($_SESSION['add-category'])) : ?>
        <div class="alert_message error">
            <p>
            <?= $_SESSION['add-category'];
            unset($_SESSION['add-category']); 
            ?>
            </p>
        </div>
        <?php endif ?>
        <div class="form-group">
          <label for="judul">Nama Kategori</label>
          <input type="text" id="nama_kategori" name="title" value="<?= $title ?>" placeholder="Masukkan nama kategori"
          />
          <label for="body">Deskripsi</label>
          <textarea rows="5" name="description" value="<?= $description ?>" id="description" placeholder="Tuliskan deskripsi kategori di sini..."><?= $description ?></textarea>  
        </div>
        <button type="submit" class="form-btn" name="submit">Tambah Kategori</button>
      </form>
    </div>
  </body>
</html>
