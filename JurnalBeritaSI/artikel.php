<?php 
include 'partials/header.php'; 

$current_user_id = $_SESSION['user-id'] ?? null;

// fetch post from database if id is set
if(isset($_GET['id'])) {
  $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
  $query = "SELECT * FROM post WHERE id=$id";
  $result = mysqli_query($connection, $query);
  $post = mysqli_fetch_assoc($result);

  // Logika untuk memperbarui tampilan unik
  if ($current_user_id) {
    // Pengguna yang masuk
    $check_view_query = "SELECT id FROM post_views WHERE post_id = $id AND user_id = $current_user_id";
    $check_view_result = mysqli_query($connection, $check_view_query);

    if (mysqli_num_rows($check_view_result) == 0) {
      // Pengguna belum melihat postingan ini, perbarui tampilan dan catat
      $update_views_query = "UPDATE post SET views = views + 1 WHERE id = $id";
      mysqli_query($connection, $update_views_query);

      $insert_view_query = "INSERT INTO post_views (post_id, user_id) VALUES ($id, $current_user_id)";
      mysqli_query($connection, $insert_view_query);
    }
  } else {
    // Pengguna anonim (tidak masuk)
    if (!isset($_SESSION['viewed_posts'])) {
      $_SESSION['viewed_posts'] = [];
    }

    if (!in_array($id, $_SESSION['viewed_posts'])) {
      // Artikel belum dilihat dalam sesi ini, perbarui tampilan dan catat di sesi
      $update_views_query = "UPDATE post SET views = views + 1 WHERE id = $id";
      mysqli_query($connection, $update_views_query);

      $_SESSION['viewed_posts'][] = $id;
    }
  }

} else {
  header('location: ' . ROOT_URL . 'artikel.php');
  die();
}

$current_post_id = $post['id'];

$average_rating = 0;
$user_rating = 0;

// Fetch average rating for the current post
$avg_query = "SELECT AVG(rating) as average_rating FROM post_ratings WHERE post_id = $current_post_id";
$avg_result = mysqli_query($connection, $avg_query);
if ($avg_result) {
    $avg_row = mysqli_fetch_assoc($avg_result);
    $average_rating = round($avg_row['average_rating'] ?? 0, 1);
}

// Fetch user's rating for the current post if user is logged in
if ($current_user_id) {
    $user_rating_query = "SELECT rating FROM post_ratings WHERE post_id = $current_post_id AND user_id = $current_user_id";
    $user_rating_result = mysqli_query($connection, $user_rating_query);
    if ($user_rating_result && mysqli_num_rows($user_rating_result) > 0) {
        $user_rating_row = mysqli_fetch_assoc($user_rating_result);
        $user_rating = $user_rating_row['rating'];
    }
}

// fetch latest posts published on the current date
$query_latest = "SELECT * FROM post WHERE DATE(date_time) = CURDATE() ORDER BY date_time DESC LIMIT 4";
$posts_latest = mysqli_query($connection, $query_latest);

$query = "SELECT
            p.*,
            AVG(pr.rating) AS average_rating,
            COUNT(c.id) AS comment_count
          FROM
            post p
          LEFT JOIN
            post_ratings pr ON p.id = pr.post_id
          LEFT JOIN
            comments c ON p.id = c.post_id
          GROUP BY
            p.id
          HAVING
            average_rating >= 3.5 AND comment_count >= 5
          ORDER BY
            average_rating DESC, comment_count DESC LIMIT 4";
$posts = mysqli_query($connection, $query);
?>

  
   

  <main>
    <h1><?= $post['title'] ?></h1>
    <div class="box">
      <div class="container">
        <div class="text">
          <div class="img-artikel">
          <?php
            $extension = pathinfo($post['thumbnail'], PATHINFO_EXTENSION);
            $allowed_videos = ['mp4', 'mov', 'avi'];
            
            if (in_array(strtolower($extension), $allowed_videos)) {
                echo '<video controls width="100%">
                        <source src="images/' . $post['thumbnail'] . '" type="video/' . $extension . '">
                        Browser Anda tidak mendukung tag video.
                      </video>';
            } else {
                echo '<img src="images/' . $post['thumbnail'] . '" alt="Thumbnail">';
            }
            ?>
            </div>
          <div class="views">
            <small>Dilihat: <?= $post['views'] ?></small>
          </div>
          <div class="button-container">
            <div class="rating-container">
              <div class="rating" id="ratingContainer" data-post-id="<?= $current_post_id ?>" data-user-id="<?= $current_user_id ?>" data-initial-rating="<?= $user_rating ?>" data-average-rating="<?= $average_rating ?>">
                <i class="bx bx-star" data-rating="1"></i>
                <i class="bx bx-star" data-rating="2"></i>
                <i class="bx bx-star" data-rating="3"></i>
                <i class="bx bx-star" data-rating="4"></i>
                <i class="bx bx-star" data-rating="5"></i>
              </div>
              <div class="average-rating-display-container">
                <span class="average-rating-display" id="averageRatingDisplay">Rata-rata: <?= $average_rating ?></span>
              </div>
            </div>
              <button id="shareBtn">
              <i class="uil uil-share-alt"></i>
              </button>
          </div>
          <p class="isi"><?= html_entity_decode($post['body']) ?></p>
        </div>

        <!-- Bagian berita terbaru -->
        <div class="container1">

          <h1>BERITA TERBARU</h1>
          <?php while($post = mysqli_fetch_assoc($posts_latest)) : ?>
          <div class="berita">
            <div class="img-berita">
            <?php
            $extension = pathinfo($post['thumbnail'], PATHINFO_EXTENSION);
            $allowed_videos = ['mp4', 'mov', 'avi'];
            
            if (in_array(strtolower($extension), $allowed_videos)) {
                echo '<video controls width="100%">
                        <source src="images/' . $post['thumbnail'] . '" type="video/' . $extension . '">
                        Browser Anda tidak mendukung tag video.
                      </video>';
            } else {
                echo '<img src="images/' . $post['thumbnail'] . '" alt="Thumbnail">';
            }
            ?>
            </div>
            <h2><a
                href="<?= ROOT_URL ?>artikel.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a></h2>
          </div>
          <?php endwhile; ?>

          <!-- Bagian berita terpopuler -->
          <h1>BERITA TERPOPULER</h1>
          <?php while($post = mysqli_fetch_assoc($posts)) : ?>
          <div class="berita">
            <div class="img-berita">
            <?php
            $extension = pathinfo($post['thumbnail'], PATHINFO_EXTENSION);
            $allowed_videos = ['mp4', 'mov', 'avi'];
            
            if (in_array(strtolower($extension), $allowed_videos)) {
                echo '<video controls width="100%">
                        <source src="images/' . $post['thumbnail'] . '" type="video/' . $extension . '">
                        Browser Anda tidak mendukung tag video.
                      </video>';
            } else {
                echo '<img src="images/' . $post['thumbnail'] . '" alt="Thumbnail">';
            }
            ?>
            </div>
            <h2><a
                href="<?= ROOT_URL ?>artikel.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a></h2>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </main>

  <!-- Bagian komentar -->
  <div class="comment-section">
    <h2>Komentar</h2>
    <?php if(isset($_SESSION['add-comment'])) : ?>
    <div class="alert_message error">
      <p>
        <?= $_SESSION['add-comment'];
        unset($_SESSION['add-comment']);
        ?>
      </p>
    </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['add-comment-success'])) : ?>
    <div class="alert_message success">
      <p>
        <?= $_SESSION['add-comment-success'];
        unset($_SESSION['add-comment-success']);
        ?>
      </p>
    </div>
    <?php endif; ?>
    <form action="<?= ROOT_URL ?>comment-logic.php" enctype="multipart/form-data" method="POST">
      <input type="hidden" name="post_id" value="<?= $current_post_id ?>">
      <input type="hidden" name="user_id" value="<?= $current_user_id ?>">
      <div class="comment-form">
        <input type="text" name="comment" placeholder="Tambahkan komentar...">
        <button class="btn" name="submit">Kirim</button>
      </div>
    </form>
  </div>

  <?php
            // Query untuk mengambil komentar yang sesuai dengan post ini
            $comments_query = "SELECT c.*, u.username, u.avatar 
                               FROM comments c 
                               JOIN users u ON c.user_id = u.id 
                               WHERE c.post_id = $current_post_id 
                               ORDER BY c.date_time DESC";
            $comments_result = mysqli_query($connection, $comments_query);

            // Hitung jumlah komentar
            $comment_count = mysqli_num_rows($comments_result);
            ?>

  <!-- Bagian list komentar -->
  <?php while($comment = mysqli_fetch_assoc($comments_result)) : ?>
  <div class="comment-list">
    <div class="profile">
      <img src="images/<?= $comment['avatar'] ?>" alt="">
      <p><?= $comment['username'] ?></p>
    </div>
    <div class="comment">
      <div class="comment-actions">
        <input type="checkbox" class="menu-toggle" aria-label="Toggle menu komentar" />
        <span class="kebab-btn" aria-hidden="true"><span class="kebab-dots"></span></span>
        <div class="menu-dropdown" role="menu">
          <a href="<?= ROOT_URL ?>delete-comment.php?id=<?= $comment['id'] ?>" class="danger" role="menuitem">Hapus</a>
        </div>
      </div>
      <p><?= $comment['comment'] ?></p>
      <button class="reply-btn btn sm" >Balas</button>
    </div>

    <div class="replies-container">
      <div class="comment-form reply-form is-hidden">
        <form action="<?= ROOT_URL ?>reply-komentar-logic.php" method="POST">
        <input type="hidden" name="post_id" value="<?= $current_post_id ?>">
        <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
          <input type="text" name="reply" placeholder="Tambahkan balasan...">
          <button type="submit" class="btn sm">Kirim</button>
        </form>
      </div>

      <?php
      $replies_query = "SELECT r.*, u.username, u.avatar 
      FROM replies r 
      JOIN users u ON r.user_id = u.id 
      WHERE r.parent_id = {$comment['id']} 
      ORDER BY r.date_time ASC";
      $replies_result = mysqli_query($connection, $replies_query);
      ?>
      <div class="reply-list">
        <div class="comment-list nested-reply">
        <?php while ($reply = mysqli_fetch_assoc($replies_result)): ?>
          <div class="profile">
            <img src="images/<?= $reply['avatar'] ?>" alt="">
            <p><?= $reply['username'] ?></p>
          </div>
          <div class="comment">
          <div class="comment-actions">
          <input type="checkbox" class="menu-toggle" aria-label="Toggle menu komentar" />
          <span class="kebab-btn" aria-hidden="true"><span class="kebab-dots"></span></span>
            <div class="menu-dropdown" role="menu">
              <a href="<?= ROOT_URL ?>delete-reply.php?id=<?= $reply['id'] ?>" class="danger" role="menuitem">Hapus</a>
            </div>
          </div>
            <p><?= $reply['reply'] ?></p>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; ?>

  <footer>
    <p>&copy; 2025 Sistem Informasi. Semua hak dilindungi.</p>
    <div class="social-media">
      <a href="#">Facebook</a>
      <a href="#">Twitter</a>
      <a href="#">Instagram</a>
    </div>
  </footer>
  <script src="Javascript/script.js"></script>
</body>
</html>