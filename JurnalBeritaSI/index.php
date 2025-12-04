<?php 
include 'partials/header.php'; 

// fetch featured post from database
$featured_query = "SELECT * FROM post WHERE is_featured=1";
$featured_result = mysqli_query($connection, $featured_query);
$i = 0; 

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
      <section class="slider">
      <?php while($post_slider = mysqli_fetch_assoc($featured_result)) : ?>
        <div class="slider-item <?= $i === 0 ? 'active' : '' ?>">
          <div class="slider-image">
            <?php
            $extension = pathinfo($post_slider['thumbnail'], PATHINFO_EXTENSION);
            $allowed_videos = ['mp4', 'mov', 'avi'];
            
            if (in_array(strtolower($extension), $allowed_videos)) {
              echo '<video controls width="100%">
              <source src="images/' . $post_slider['thumbnail'] . '" type="video/' . $extension . '">
              Browser Anda tidak mendukung tag video.
              </video>';
            } else {
              echo '<img src="images/' . $post_slider['thumbnail'] . '" alt="Thumbnail">';
            }
            ?>
            </div>
          <h2>
          <a href="<?= ROOT_URL ?>artikel.php?id=<?= $post_slider['id'] ?>"><?= $post_slider['title'] ?></a>
          </h2>
        </div>
        <?php $i++; ?>
        <?php endwhile; ?>
      </section>

      <section class="latest-news">
        <h2>Berita Terbaru Sistem Informasi</h2>
        <?php while($post = mysqli_fetch_assoc($posts_latest)) : ?>
        <div class="news-item">
          <div class="post_thumbnail">
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
          <div class="news-text-content">
          <h3 class="post_title"><a href="<?= ROOT_URL ?>artikel.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a></h3>
          <small><?php echo $post['date_time']; ?></small>
          </div>
        </div>
        <?php endwhile; ?>
      </section>

      <section class="popular-news">
        <h2>Berita Populer Sistem Informasi</h2>
        <?php while($post = mysqli_fetch_assoc($posts)) : ?>
        <div class="news-item">
          <div class="post_thumbnail">
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
          <div class="news-text-content">
          <h3 class="post_title"><a href="<?= ROOT_URL ?>artikel.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a></h3>
          <small><?php echo $post['date_time']; ?></small>
          </div>
        </div>
        <?php endwhile; ?>
      </section>
    </main>

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
