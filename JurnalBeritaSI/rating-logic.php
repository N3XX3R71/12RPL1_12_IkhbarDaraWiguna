<?php
require 'config/database.php';

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = filter_var($_POST['post_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($_POST['user_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
    $rating = filter_var($_POST['rating'] ?? null, FILTER_SANITIZE_NUMBER_INT);

    if (!$post_id || !$user_id || !$rating || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        exit();
    }

    // Cek apakah pengguna sudah memberi rating pada postingan ini
    $check_query = "SELECT * FROM post_ratings WHERE post_id = $post_id AND user_id = $user_id";
    $check_result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Pengguna sudah memberi rating, jadi update ratingnya
        $update_query = "UPDATE post_ratings SET rating = $rating WHERE post_id = $post_id AND user_id = $user_id";
        if (mysqli_query($connection, $update_query)) {
            // Berhasil update
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update rating.']);
            exit();
        }
    } else {
        // Pengguna belum memberi rating, jadi insert rating baru
        $insert_query = "INSERT INTO post_ratings (post_id, user_id, rating) VALUES ($post_id, $user_id, $rating)";
        if (mysqli_query($connection, $insert_query)) {
            // Berhasil insert
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert rating.']);
            exit();
        }
    }

    // Hitung rata-rata rating untuk postingan ini
    $avg_rating_query = "SELECT AVG(rating) as average_rating FROM post_ratings WHERE post_id = $post_id";
    $avg_rating_result = mysqli_query($connection, $avg_rating_query);
    $avg_rating_row = mysqli_fetch_assoc($avg_rating_result);
    $average_rating = round($avg_rating_row['average_rating'], 1); // Bulatkan ke satu desimal

    echo json_encode(['success' => true, 'average_rating' => $average_rating, 'user_rating' => $rating]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
