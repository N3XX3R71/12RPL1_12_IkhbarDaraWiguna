<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = $_POST['body'];
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    // set is_featured to 0 if unchecked
    $is_featured = $is_featured == 1 ?: 0;

    // validate form data
    if(!$title) {
        $_SESSION['add-post'] = "Enter post title";
    } elseif (!$category_id) {
        $_SESSION['add-post'] = "Select post category";
    } elseif (!$body) {
        $_SESSION['add-post'] = "Enter post body";
    } elseif (!$thumbnail['name']) {
        $_SESSION['add-post'] = "Choose post thumbnail";
    } else { 
        // Escape title and body to prevent SQL injection
        $title = mysqli_real_escape_string($connection, $title);
        $body = mysqli_real_escape_string($connection, $body);

        // PROSES THUMBNAIL
        // Ubah nama file
        $time = time(); // Membuat nama file unik
        $thumbnail_name = $time . $thumbnail['name'];
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination_path = '../images/' . $thumbnail_name;

        // Pastikan file adalah gambar atau video
        $allowed_files = ['png', 'jpg', 'jpeg', 'mp4', 'mov', 'avi'];
        $extension = explode('.', $thumbnail_name);
        $extension = strtolower(end($extension));
        
        if(in_array($extension, $allowed_files)) {
            // Pastikan ukuran file tidak terlalu besar (150MB+)
            if($thumbnail['size'] < 150_000_000) {
                // Upload thumbnail
                move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
            } else {
                $_SESSION['add-post'] = "Ukuran file terlalu besar. Harus kurang dari 100MB";
            }
        } else {
            $_SESSION['add-post'] = "File harus berupa png, jpg, jpeg, mp4, mov, atau avi";
        } 
    }
    // redirect back (with form data) to add-post page if there is any problem
    if (isset($_SESSION['add-post'])) {
        $_SESSION['add-post-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/add-post.php');
        die();
    } else {
        // set is_featured of all post to 0 if is_featured for this post is 1
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE post SET is_featured=0";
            $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        // insert post into database
        $query = "INSERT INTO post (title, body, thumbnail, category_id, author_id, is_featured, views) VALUES ('$title', '$body', '$thumbnail_name', $category_id, $author_id, $is_featured, 0)";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $_SESSION['add-post-success'] = "New post added successfully";
            header('location: ' . ROOT_URL . 'admin/');
            die();
        }
    }
}

header('location: ' . ROOT_URL . 'admin/add-post.php');
die();
