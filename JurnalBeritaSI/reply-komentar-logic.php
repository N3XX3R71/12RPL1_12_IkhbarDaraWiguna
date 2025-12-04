<?php
include 'config/database.php'; 

if (isset($_POST['reply'])) {
    // Ambil data dari form
    $post_id = filter_var($_POST['post_id'], FILTER_SANITIZE_NUMBER_INT);
    $parent_id = filter_var($_POST['parent_id'], FILTER_SANITIZE_NUMBER_INT);
    $reply = filter_var($_POST['reply'], FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user-id']; 

    $query = "INSERT INTO replies (post_id, parent_id, user_id, reply, date_time) 
              VALUES ($post_id, $parent_id, $user_id, '$reply', NOW())";

    if (mysqli_query($connection, $query)) {
        // Jika berhasil, redirect kembali ke halaman post
        header("Location: " . ROOT_URL . "artikel.php?id=$post_id");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . mysqli_error($connection);
    }
} else {
    // Jika tidak ada data yang dikirim, redirect kembali
    header("Location: " . ROOT_URL . 'artikel.php?id=' . $post_id);
    exit();
}
?>