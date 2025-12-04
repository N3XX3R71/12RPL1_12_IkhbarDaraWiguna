<?php
    require 'config/database.php';

    // pagination
    $per_page = 6;
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($current_page - 1) * $per_page;

    // fetch all comments from database
    $query = "SELECT c.id, c.comment, u.username FROM comments c JOIN users u ON c.user_id = u.id ORDER BY c.id DESC LIMIT $per_page OFFSET $offset";
    $comments = mysqli_query($connection, $query);

    // fetch total comments for pagination
    $total_comments_query = "SELECT COUNT(*) FROM comments";
    $total_comments_result = mysqli_query($connection, $total_comments_query);
    $total_comments = mysqli_fetch_row($total_comments_result)[0];
    $total_pages = ceil($total_comments / $per_page);

    // fetch current user's posts from database
    $current_user_id = $_SESSION['user-id'];
    $query = "SELECT id, title, category_id FROM post WHERE author_id=$current_user_id ORDER BY id DESC";
    $posts = mysqli_query($connection, $query);

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
          <a href="manage-comment.php" class="active"
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
      <h2>Manage Comment</h2>
      <?php if(mysqli_num_rows($comments) > 0) : ?>
      <table class="user-table">
        <thead>
          <tr>
            <th scope="col">Username</th>
            <th scope="col">Comment</th>
            <th scope="col">Delete</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($comment = mysqli_fetch_assoc($comments)) : ?>
          <tr>
            <td data-label="Username"><?= $comment['username'] ?></td>
            <td data-label="Comment"><?= $comment['comment'] ?></td>
            <td data-label="Delete">
              <a href="<?= ROOT_URL ?>delete-comment.php?id=<?= $comment['id'] ?>" onclick="return confirm('Are you sure you want to delete this comment?')" class="btn sm danger">Delete</a>
            </td>
          </tr>
          <?php endwhile ?>
        </tbody>
      </table>
      <?php else : ?>
        <div class="alert_message error"><?= "No comments found" ?></div>
      <?php endif ?>
      <?php if(mysqli_num_rows($comments) > 0) : ?>
      <div class="pagination-wrapper">
        <div aria-label="...">
          <ul class="pagination">
            <?php if ($current_page > 1) : ?>
              <li class="page-item">
                <a href="<?= ROOT_URL ?>admin/manage-comment.php?page=<?= $current_page - 1 ?>" class="page-link">Previous</a>
              </li>
            <?php endif ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
              <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= ROOT_URL ?>admin/manage-comment.php?page=<?= $i ?>" <?= ($i == $current_page) ? 'aria-current="page"' : '' ?>><?= $i ?></a>
              </li>
            <?php endfor ?>

            <?php if ($current_page < $total_pages) : ?>
              <li class="page-item">
                <a class="page-link" href="<?= ROOT_URL ?>admin/manage-comment.php?page=<?= $current_page + 1 ?>">Next</a>
              </li>
            <?php endif ?>
          </ul>
        </div>
      </div>
      <?php endif ?>
    </div>
  </body>
</html>
