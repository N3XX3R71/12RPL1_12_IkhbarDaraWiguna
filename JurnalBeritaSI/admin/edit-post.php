<?php
    require 'config/database.php';

    // fetch categories from database 
    $category_query = "SELECT * FROM categories";
    $categories = mysqli_query($connection, $category_query);

    // fetch post data from database if id is set
    if (isset($_GET['id'])) {
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $query = "SELECT * FROM post WHERE id=$id";
        $result = mysqli_query($connection, $query);
        $post = mysqli_fetch_assoc($result);
    } else {
        header('location: ' . ROOT_URL . 'admin/');
        die();
    }                 
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Post</title>
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/mcj6ku4tidldpn65pg3u7krfyjy13iffe0xzn9lza1cj8a36/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="center-wrapper">
      <form class="form-container" action="<?= ROOT_URL ?>admin/edit-post-logic.php" enctype="multipart/form-data" method="POST">
        <h2>Edit Post</h2>
        <input type="hidden" name="id" value="<?= $post['id'] ?>">
        <div class="form-group">
          <label for="judul">Judul</label>
          <input
            type="text"
            id="judul"
            name="title"
            value="<?= $post['title'] ?>"
            required
            autocomplete="judul"
            placeholder="Masukkan judul artikel"
          />
          <label for="kategori">Kategori</label>
          <select name="category" id="kategori" required>
          <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
          <?php endwhile ?>
          </select>
          <label for="body">Isi Artikel</label>
          <textarea rows="50" name="body" id="body" placeholder="Tuliskan isi artikel di sini..."><?= $post['body'] ?></textarea>
          <div class="form_control inline">
                <input type="checkbox" name="is_featured" value="1" id="is_featured" <?= $post['is_featured'] == 1 ? 'checked' : '' ?>>
                <label for="is_featured">Featured</label>
            </div>
          <label for="gambar">Ganti Gambar</label>
          <input type="file" name="thumbnail" id="gambar">
          <input type="hidden" name="previous_thumbnail_name" value="<?= $post['thumbnail'] ?>">
        </div>
        <button type="submit" class="form-btn" name="submit">Edit Post</button>
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
