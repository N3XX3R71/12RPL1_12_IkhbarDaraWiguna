<?php
    require 'config/database.php';

    // pagination
    $per_page = 6;
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($current_page - 1) * $per_page;

    // fetch current user's posts from database
    $current_user_id = $_SESSION['user-id'];
    $query = "SELECT id, title, category_id FROM post WHERE author_id=$current_user_id ORDER BY id DESC LIMIT $per_page OFFSET $offset";
    $posts = mysqli_query($connection, $query);

    // fetch total posts for pagination
    $total_posts_query = "SELECT COUNT(*) FROM post WHERE author_id=$current_user_id";
    $total_posts_result = mysqli_query($connection, $total_posts_query);
    $total_posts = mysqli_fetch_row($total_posts_result)[0];
    $total_pages = ceil($total_posts / $per_page);

    // fetch user from database
    $query = "SELECT * FROM users WHERE id = $current_user_id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);
    $avatar = $user['avatar'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
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
            ><i class="uil uil-pen"></i> <span>Add Post</span></a>
        </li>
        <li>
          <a href="index.php" class="active"
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

    <div class="manage-post-section">
      <h2>Manage Post</h2>
      <?php if(isset($_SESSION['add-post-success'])) : ?>
        <div class="alert_message success">
          <?= $_SESSION['add-post-success']; ?>
          <?php unset($_SESSION['add-post-success']); ?>
        </div>
      <?php elseif(isset($_SESSION['edit-post-success'])) : ?>
        <div class="alert_message success">
          <?= $_SESSION['edit-post-success']; ?>
          <?php unset($_SESSION['edit-post-success']); ?>
        </div>
      <?php elseif(isset($_SESSION['delete-post-success'])) : ?>
        <div class="alert_message success">
          <?= $_SESSION['delete-post-success']; ?>
          <?php unset($_SESSION['delete-post-success']); ?>
        </div>
      <?php elseif(isset($_SESSION['delete-post-error'])) : ?>
        <div class="alert_message error">
          <?= $_SESSION['delete-post-error']; ?>
          <?php unset($_SESSION['delete-post-error']); ?>
        </div>
      <?php endif ?>
      <?php if(mysqli_num_rows($posts) > 0) : ?>
      <table class="user-table">
        <thead>
          <tr>
            <th scope="col">Judul</th>
            <th scope="col">Kategori</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($post = mysqli_fetch_assoc($posts)) : ?>
            <!-- get category title of each post from categories table -->
            <?php
            $category_id = $post['category_id'];
            $category_query = "SELECT title FROM categories WHERE id=$category_id";
            $category_result = mysqli_query($connection, $category_query);
            $category = mysqli_fetch_assoc($category_result);
            ?>
          <tr>
            <td data-label="Judul">
              <?= $post['title'] ?>
            </td>
            <td data-label="Kategori">
              <?= $category['title'] ?>
            </td>
            <td data-label="Edit"><a href="<?= ROOT_URL ?>admin/edit-post.php?id=<?= $post['id'] ?>" class="btn sm">Edit</a></td>
            <td data-label="Delete">
              <a href="<?= ROOT_URL ?>admin/delete-post.php?id=<?= $post['id'] ?>" onclick="return confirm('Are you sure you want to delete this post?')" class="btn sm danger">Delete</a>
            </td>
          </tr>
          <?php endwhile ?>
        </tbody>
      </table>
      <?php else : ?>
        <div class="alert_message error"><?= "No posts found" ?></div>
      <?php endif ?>

      <?php if(mysqli_num_rows($posts) > 0) : ?>
      <div class="pagination-wrapper">
        <div aria-label="...">
          <ul class="pagination">
            <?php if ($current_page > 1) : ?>
              <li class="page-item">
                <a href="<?= ROOT_URL ?>admin/index.php?page=<?= $current_page - 1 ?>" class="page-link">Previous</a>
              </li>
            <?php endif ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
              <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= ROOT_URL ?>admin/index.php?page=<?= $i ?>" <?= ($i == $current_page) ? 'aria-current="page"' : '' ?>><?= $i ?></a>
              </li>
            <?php endfor ?>

            <?php if ($current_page < $total_pages) : ?>
              <li class="page-item">
                <a class="page-link" href="<?= ROOT_URL ?>admin/index.php?page=<?= $current_page + 1 ?>">Next</a>
              </li>
            <?php endif ?>
          </ul>
        </div>
      </div>
      <?php endif ?>
    </div>
  </body>
</html>
