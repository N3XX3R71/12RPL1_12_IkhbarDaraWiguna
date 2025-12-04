<?php
require 'config/database.php';

if (!isset($_SESSION['user-id'])) {
    $_SESSION['add-comment'] = "Anda harus login terlebih dahulu untuk menambahkan komentar";
    header('location: ' . ROOT_URL . 'signin.php');
    die();
}

if (isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $post_id = filter_var($_POST['post_id'], FILTER_SANITIZE_NUMBER_INT);

    // validate form data
    if(!$comment) {
        $_SESSION['add-comment'] = "Enter comment";
    }

    // redirect back (with form data) to add-post page if there is any problem
    if (isset($_SESSION['add-comment'])) {
        $_SESSION['add-comment-data'] = $_POST;
        header('location: ' . ROOT_URL . 'artikel.php?id=' . $post_id);
        die();
    } else {
        // insert post into database
        $query = "INSERT INTO comments (comment, user_id, post_id) VALUES ('$comment', $author_id, $post_id)";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $_SESSION['add-comment-success'] = "New comment added successfully";
            header('location: ' . ROOT_URL . 'artikel.php?id=' . $post_id);
            die();
        }
    }
} else if (isset($_POST['submit_reply'])) {
    $author_id = $_SESSION['user-id'];
    $reply = filter_var($_POST['reply'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $post_id = filter_var($_POST['post_id'], FILTER_SANITIZE_NUMBER_INT);
    $parent_id = filter_var($_POST['parent_id'], FILTER_SANITIZE_NUMBER_INT);

    // validate form data
    if(!$reply) {
        $_SESSION['add-comment'] = "Enter reply";
    }

    // redirect back (with form data) to add-post page if there is any problem
    if (isset($_SESSION['add-comment'])) {
        $_SESSION['add-comment-data'] = $_POST;
        header('location: ' . ROOT_URL . 'artikel.php?id=' . $post_id);
        die();
    } else {
        // insert reply into database
        $query = "INSERT INTO replies (reply, user_id, post_id, parent_id) VALUES ('$reply', $author_id, $post_id, $parent_id)";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $_SESSION['add-comment-success'] = "New reply added successfully";
            header('location: ' . ROOT_URL . 'artikel.php?id=' . $post_id);
            die();
        }
    }
}

header('location: ' . ROOT_URL . 'artikel.php?id=' . $post_id);
die();
