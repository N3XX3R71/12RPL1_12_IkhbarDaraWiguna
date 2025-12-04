<?php
    require 'config/database.php';

    // pagination
    $per_page = 6;
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($current_page - 1) * $per_page;

    // fetch current user's posts from database
    $current_user_id = $_SESSION['user-id'];
    $query = "SELECT id, username, email, is_admin FROM users ORDER BY id DESC LIMIT $per_page OFFSET $offset";
    $users = mysqli_query($connection, $query); 

    // fetch total users for pagination
    $total_users_query = "SELECT COUNT(*) FROM users";
    $total_users_result = mysqli_query($connection, $total_users_query);
    $total_users = mysqli_fetch_row($total_users_result)[0];
    $total_pages = ceil($total_users / $per_page);

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
          <a href="manage-users.php" class="active"
            ><i class="uil uil-users-alt"></i>
            <span>Manage Users</span></a>
        </li>
        <li>
          <a href="manage-comment.php"
            ><i class="uil uil-comment-alt-notes"></i>
            <span>Manage Comment</span></a
          >
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
      <h2>Manage Users</h2>
      <?php if (isset($_SESSION['add-user-success'])) : ?>
        <div class="alert_message success">
          <p>
            <?= $_SESSION['add-user-success'];
            unset($_SESSION['add-user-success']);
            ?>
          </p>
        </div>
      <?php elseif (isset($_SESSION['add-user'])) : ?>
        <div class="alert_message error">
          <p>
            <?= $_SESSION['add-user'];
            unset($_SESSION['add-user']);
            ?>
          </p>
        </div>
      <?php elseif (isset($_SESSION['edit-user-success'])) : ?>
        <div class="alert_message success">
          <p>
            <?= $_SESSION['edit-user-success'];
            unset($_SESSION['edit-user-success']);
            ?>
          </p>
        </div>
      <?php elseif (isset($_SESSION['edit-user'])) : ?>
        <div class="alert_message error">
          <p>
            <?= $_SESSION['edit-user'];
            unset($_SESSION['edit-user']);
            ?>
          </p>
        </div>
      <?php elseif (isset($_SESSION['delete-user-success'])) : ?>
        <div class="alert_message success">
          <p>
            <?= $_SESSION['delete-user-success'];
            unset($_SESSION['delete-user-success']);
            ?>
          </p>
        </div>
      <?php elseif (isset($_SESSION['delete-user'])) : ?>
        <div class="alert_message error">
          <p>
            <?= $_SESSION['delete-user'];
            unset($_SESSION['delete-user']);
            ?>
          </p>
        </div>
      <?php endif ?>
      <?php if(mysqli_num_rows($users) > 0) : ?>
      <table class="user-table">
        <thead>
          <tr>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($user = mysqli_fetch_assoc($users)) : ?>
          <tr>
            <td data-label="Judul">
              <?= $user['username'] ?>
            </td>
            <td data-label="Email"><?= $user['email'] ?></td>
            <td data-label="Role"><?= $user['is_admin'] == 1 ? 'Admin' : 'Visitor' ?></td>
            <td data-label="Edit"><a href="<?= ROOT_URL ?>admin/edit-user.php?id=<?= $user['id'] ?>" class="btn sm">Edit</a></td>
            <td data-label="Delete">
              <a href="<?= ROOT_URL ?>admin/delete-user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="btn sm danger">Delete</a>
            </td>
          </tr>
          <?php endwhile ?>
        </tbody>
      </table>
      <?php else : ?>
        <div class="alert_message error"><?= "No users found" ?></div>
      <?php endif ?>
      <?php if(mysqli_num_rows($users) > 0) : ?>
      <div class="pagination-wrapper">
        <div aria-label="...">
          <ul class="pagination">
            <?php if ($current_page > 1) : ?>
              <li class="page-item">
                <a href="<?= ROOT_URL ?>admin/manage-users.php?page=<?= $current_page - 1 ?>" class="page-link">Previous</a>
              </li>
            <?php endif ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
              <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= ROOT_URL ?>admin/manage-users.php?page=<?= $i ?>" <?= ($i == $current_page) ? 'aria-current="page"' : '' ?>><?= $i ?></a>
              </li>
            <?php endfor ?>

            <?php if ($current_page < $total_pages) : ?>
              <li class="page-item">
                <a class="page-link" href="<?= ROOT_URL ?>admin/manage-users.php?page=<?= $current_page + 1 ?>">Next</a>
              </li>
            <?php endif ?>
          </ul>
        </div>
      </div>
      <?php endif ?>
    </div>
  </body>
</html>
