<?php 
include 'partials/header.php'; 

// fetch posts from posts table
$query = "SELECT * FROM post ORDER BY date_time DESC";
$posts = mysqli_query($connection, $query);
?>

    <section class="search_bar">
      <form class="container search_bar-container" action="search.php" method="GET">
        <div>
          <i class="uil uil-search"></i>
          <input type="search" name="search" placeholder="Search" />
        </div>
        <button type="submit" name="submit" class="btn-search">Cari</button>
      </form>
    </section>
    <!-- =========== END OF SEARCH =========== -->

    <section class="post">
      <div class="container three-posts-row">
      <?php while($post = mysqli_fetch_assoc($posts)) : ?>
        <article class="post">
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
          <?php 
                // fetch category from categories table using category_id of post
                $category_id = $post['category_id'];
                $category_query = "SELECT * FROM categories WHERE id=$category_id";
                $category_result = mysqli_query($connection, $category_query);
                $category = mysqli_fetch_assoc($category_result);
                ?>
          <div class="post_info">
          <?php if ($category && $category['title'] !== 'No Category') : ?>
            <a href="<?= ROOT_URL ?>category.php?id=<?= $category['id'] ?>" class="category_button"><?= $category['title'] ?></a>
          <?php endif; ?>
          <h3 class="post_title">
            <a href="<?= ROOT_URL ?>artikel.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a>
            </h3>
            <div class="post_author">
              <div class="post_author-info">
                <small>
                  <?php
                    $timestamp = strtotime($post['date_time']);
                    $day = date('d', $timestamp);
                    $month_num = date('n', $timestamp);
                    $year = date('Y', $timestamp);
                    $time = date('H:i', $timestamp);

                    $indonesian_months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];

                    $month_name = $indonesian_months[$month_num];

                    echo "{$day} {$month_name} {$year}, {$time} WIB";
                  ?>
                </small>
              </div>
            </div>
          </div>
        </article>
        <?php endwhile; ?>
      </div>
    </section>
    <!-- =========== END OF POST =========== -->

    <footer>
      <p>&copy; 2025 Sistem Informasi. Semua hak dilindungi.</p>
      <div class="social-media">
        <a href="#">Facebook</a>
        <a href="#">Twitter</a>
        <a href="#">Instagram</a>
      </div>
    </footer>
    <script src="javascript/script.js"></script>
  </body>
</html>
