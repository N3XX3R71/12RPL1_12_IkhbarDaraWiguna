<?php 
require 'config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM users WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);
} else {
    header('location: ' . ROOT_URL . 'admin/manage-users.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"
      rel="stylesheet"
    />
</head>
<body>
    <div class="center-wrapper">
        <div class="form-container">
            <h2>Edit User</h2>
            <?php if(isset($_SESSION['edit-user'])) : ?>
                <div class="alert_message error">
                    <p class="text-error">
                        <?= $_SESSION['edit-user'];
                        unset($_SESSION['edit-user']);
                        ?>
                    </p>
                </div>
            <?php endif ?>
            <form class="form-group" action="<?= ROOT_URL ?>admin/edit-user-logic.php" method="POST">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <label class="input-label" for="username">Username</label>
                <input class="input-field" type="text" id="username" name="username" value="<?= $user['username'] ?>" placeholder="Masukkan username" required />
                <label class="input-label" for="userrole">Role</label>
                <select class="input-field" name="userrole" id="userrole" required>
                    <option value="0" <?= $user['is_admin'] == 0 ? 'selected' : '' ?>>Visitor</option>
                    <option value="1" <?= $user['is_admin'] == 1 ? 'selected' : '' ?>>Admin</option>
                </select>
                <button class="form-btn" type="submit" name="submit">Update User</button>
            </form>
        </div>
    </div>
</body>
</html>