<?php
    require 'config/database.php';

    if(isset($_GET['id'])) {
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        // fetch category from database
        $query = "SELECT * FROM categories WHERE id=$id";
        $result = mysqli_query($connection, $query);
        if (mysqli_num_rows($result) == 1) {
            $category = mysqli_fetch_assoc($result);
        }
    } else {
        header('location: ' . ROOT_URL . 'admin/manage-categories');
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Category</title>
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body>
    <div class="center-wrapper">
      <form class="form-container" action="<?= ROOT_URL ?>admin/edit-category-logic.php" method="POST">
        <h2>Edit Category</h2>
        <input type="hidden" name="id" value="<?= $category['id'] ?>">
        <div class="form-group">
          <label for="judul">Nama Kategori</label>
          <input type="text" id="nama_kategori" name="title" value="<?= $category['title'] ?>" placeholder="Masukkan nama kategori"/>
          <label for="body">Deskripsi</label>
          <textarea rows="5" name="description" value="<?= $category['description'] ?>" id="description" placeholder="Tuliskan deskripsi kategori di sini..."><?= $category['description'] ?></textarea>
        </div>
        <button type="submit" class="form-btn" name="submit">Edit Kategori</button>
      </form>
    </div>
  </body>
</html>
