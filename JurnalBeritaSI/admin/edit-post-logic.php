<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = $_POST['body'];
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $author_id = $_SESSION['user-id'];
    // set is_featured to 0 if unchecked
    $is_featured = $is_featured == 1 ?: 0;

    // validate form data
    if(!$title) {
        $_SESSION['edit-post'] = "Enter post title";
    } elseif (!$category_id) {
        $_SESSION['edit-post'] = "Select post category";
    } elseif (!$body) {
        $_SESSION['edit-post'] = "Enter post body";
    } else {
        // delete existing thumbnail if new one is available
        if ($thumbnail['name']) {
            $previous_thumbnail_path = '../images/' . $previous_thumbnail_name;
            if (file_exists($previous_thumbnail_path)) {
                unlink($previous_thumbnail_path);
            }
        }

        // Escape title and body to prevent SQL injection
        $title = mysqli_real_escape_string($connection, $title);
        $body = mysqli_real_escape_string($connection, $body);

        // PROSES THUMBNAIL
        // Ubah nama file
        $thumbnail_name = $previous_thumbnail_name; // Default ke thumbnail sebelumnya
        if ($thumbnail['name']) { // Jika ada thumbnail baru diunggah
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
                    $_SESSION['edit-post'] = "Ukuran file terlalu besar. Harus kurang dari 100MB";
                }
            } else {
                $_SESSION['edit-post'] = "File harus berupa png, jpg, jpeg, mp4, mov, atau avi";
            }
        }
    }
    // redirect back (with form data) to add-post page if there is any problem
    if (isset($_SESSION['edit-post'])) {
        $_SESSION['edit-post-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/edit-post.php'); 
        die();
    } else {
        // set is_featured of all post to 0 if is_featured for this post is 1
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE post SET is_featured=0";
            $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        // update post in database
        $query = "UPDATE post SET title='$title', body='$body', thumbnail='$thumbnail_name', category_id=$category_id, is_featured=$is_featured WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $_SESSION['edit-post-success'] = "Post updated successfully";
            header('location: ' . ROOT_URL . 'admin/');
            die();
        }
    }
}

header('location: ' . ROOT_URL . 'admin/edit-post.php');
die();
