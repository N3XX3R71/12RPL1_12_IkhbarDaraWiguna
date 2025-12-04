<?php
require 'Config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Get post_id before deleting comment
    $get_post_id_query = "SELECT post_id FROM comments WHERE id = $id";
    $get_post_id_result = mysqli_query($connection, $get_post_id_query);
    $comment_data = mysqli_fetch_assoc($get_post_id_result);
    $post_id = $comment_data['post_id'];

    // Delete comment
    $delete_comment_query = "DELETE FROM comments WHERE id = $id LIMIT 1";
    $delete_comment_result = mysqli_query($connection, $delete_comment_query);

    // Delete replies associated with this comment
    $delete_replies_query = "DELETE FROM replies WHERE parent_id = $id";
    $delete_replies_result = mysqli_query($connection, $delete_replies_query);

    if (!mysqli_errno($connection)) {
        $_SESSION['delete-comment-success'] = "Comment deleted successfully";
    } else {
        $_SESSION['delete-comment'] = "Failed to delete comment";
    }
}

header('location: ' . ROOT_URL . 'artikel.php?id=' . $post_id);
die();
