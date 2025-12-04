<?php
    require 'config/database.php';

    // fetch user from database
    $current_user_id = $_SESSION['user-id'];
    $query = "SELECT * FROM users WHERE id = $current_user_id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);
    $avatar = $user['avatar'];

    // fetch categories from database
    $query = "SELECT * FROM categories";
    $categories = mysqli_query($connection, $query);

    // get back form data if form was invalid
    $title = $_SESSION['add-post-data']['title'] ?? null;
    $body = $_SESSION['add-post-data']['body'] ?? null;

    // delete form data session
    unset($_SESSION['add-post-data']);                 
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Post</title>
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <!-- ICONSCOUT CDN -->
    <link
      rel="stylesheet"
      href="https://unicons.iconscout.com/release/v4.0.8/css/line.css"
    />
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/mcj6ku4tidldpn65pg3u7krfyjy13iffe0xzn9lza1cj8a36/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="sidebar">
      <div class="profile-section">
        <img src="<?= ROOT_URL ?>images/<?= $avatar ?>" alt="Profile" class="profile-img" />
        <div class="profile-name"><?php echo $user['username'] ?></div>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="add-post.php" class="active"
            ><i class="uil uil-pen"></i> <span>Add Post</span></a>
        </li>
        <li>
          <a href="index.php"
            ><i class="uil uil-postcard"></i> <span>Manage Post</span></a>
        </li>
        <li>
          <a href="add-category.php"
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
      <form class="form-container" action="<?= ROOT_URL ?>admin/add-post-logic.php" enctype="multipart/form-data" method="POST">
        <h2>Add Post</h2>
        <?php if(isset($_SESSION['add-post'])) : ?>
        <div class="alert_message error">
            <p>
            <?= $_SESSION['add-post'];
            unset($_SESSION['add-post']); 
            ?>
            </p>
        </div>
        <?php endif ?>
        <div class="form-group">
          <label for="judul">Judul</label>
          <input
            type="text"
            id="judul"
            name="title"
            value="<?= $title ?>"
            required
            autocomplete="judul"
            placeholder="Masukkan judul artikel"/>
          <label for="kategori">Kategori</label>
          <select name="category" id="kategori" required>
          <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
          <?php endwhile ?>
          </select>
          <label for="body">Isi Artikel</label>
          <textarea rows="50" name="body" id="body" placeholder="Tuliskan isi artikel di sini..."><?= $body ?></textarea>
          <div class="form_control inline">
                <input type="checkbox" name="is_featured" value="1" id="is_featured" checked>
                <label for="is_featured">Featured</label>
            </div>
          <label for="gambar">Gambar</label>
          <input type="file" name="thumbnail" id="gambar" required>
        </div>
        <button type="submit" class="form-btn" name="submit">Add Post</button>
      </form>
    </div>
    <script src="<?= ROOT_URL ?>js/main.js"></script>
    <script>
        tinymce.init({
            selector: '#body',
            plugins: 'advlist autolink lists link image charmap print preview anchor',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
        });
    </script>
  </body>
</html>
