<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    // get update form data
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userrole = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);

    // check for valid input
    if(!$username) {
        $_SESSION['edit-user'] = "Invalid form input on edit page.";
    } else {
        // update user
        $_query = "UPDATE users SET username='$username', is_admin=$userrole WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $_query);

        if (mysqli_errno($connection)) {
            $_SESSION['edit-user'] = "Failed to update user";
        } else {
            $_SESSION['edit-user-success'] = "User $username update successfully";
        }
    }
}

header('location: ' . ROOT_URL . 'admin/manage-users.php');
die();