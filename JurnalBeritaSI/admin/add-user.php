<?php 
require 'config/database.php';

// fetch user from database
$current_user_id = $_SESSION['user-id'];
$query = "SELECT * FROM users WHERE id = $current_user_id";
$result = mysqli_query($connection, $query);
$user = mysqli_fetch_assoc($result);
$avatar = $user['avatar'];

// get back form data if there was a registration error
$username = $_SESSION['add-user-data']['username'] ?? null;
$email = $_SESSION['add-user-data']['email'] ?? null;
$createpassword = $_SESSION['add-user-data']['createpassword'] ?? null;
$confirmpassword = $_SESSION['add-user-data']['confirmpassword'] ?? null;
// delete signup data session 
unset($_SESSION['add-user-data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>add user</title>
    <link rel="stylesheet" href="../css/style.css" />
    <!-- ICONSCOUT CDN -->
    <link
      rel="stylesheet"
      href="https://unicons.iconscout.com/release/v4.0.8/css/line.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"
      rel="stylesheet"/>
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
          <a href="add-user.php" class="active"
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
        <div class="form-container">
            <h2>Add User</h2>
            <?php if(isset($_SESSION['add-user'])) : ?>
                <div class="alert_message error">
                    <p class="text-error">
                        <?= $_SESSION['add-user'];
                        unset($_SESSION['add-user']);
                        ?>
                    </p>
                </div>
            <?php endif ?>
            <form class="form-group" action="<?= ROOT_URL ?>admin/add-user-logic.php" enctype="multipart/form-data" method="POST">
                <label class="input-label" for="username">Username</label>
                <input class="input-field" type="text" id="username" name="username" value="<?= $username ?>" placeholder="Masukkan nama pengguna" required />
                <label class="input-label" for="email">Email</label>
                <input class="input-field" type="email" id="email" name="email" value="<?= $email ?>" placeholder="Masukkan email" required />
                <label class="input-label" for="password">Password</label>
                <input class="input-field" type="password" id="password" name="createpassword" value="<?= $createpassword ?>" placeholder="Buat kata sandi" required />
                <label class="input-label" for="confirm-password">Konfirmasi Password</label>
                <input class="input-field" type="password" id="confirm-password" name="confirmpassword" value="<?= $confirmpassword ?>" placeholder="Konfirmasi kata sandi" required />
                <label class="input-label" for="userrole">Peran Pengguna</label>
                <select class="input-field" name="userrole" id="userrole" required>
                    <option value="0">Visitor</option>
                    <option value="1">Admin</option>
                </select>
                <label for="avatar">Avatar Pengguna</label>
                <input type="file" name="avatar" id="avatar">
                <button class="form-btn" type="submit" name="submit">Add User</button>
            </form>
        </div>
    </div>
</body>
</html>