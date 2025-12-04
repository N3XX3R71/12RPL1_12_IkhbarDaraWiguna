<?php
require 'Config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Get post_id before deleting reply
    $get_post_id_query = "SELECT post_id FROM replies WHERE id = $id";
    $get_post_id_result = mysqli_query($connection, $get_post_id_query);
    $reply_data = mysqli_fetch_assoc($get_post_id_result);
    $post_id = $reply_data['post_id'];

    // Delete reply
    $delete_reply_query = "DELETE FROM replies WHERE id = $id LIMIT 1";
    $delete_reply_result = mysqli_query($connection, $delete_reply_query);

    if (!mysqli_errno($connection)) {
        $_SESSION['delete-reply-success'] = "Reply deleted successfully";
    } else {
        $_SESSION['delete-reply'] = "Failed to delete reply";
    }
}

header('location: ' . ROOT_URL . 'artikel.php?id=' . $post_id);
die();
