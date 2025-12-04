<?php 
include 'partials/header.php'; 

// fetch posts if id is set
if(isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM post WHERE category_id=$id ORDER BY date_time DESC";
    $posts = mysqli_query($connection, $query);
} else {
    header('location: ' . ROOT_URL . 'sistem-informasi.php');
    die();
}
?>

<header class="category_title">
    <h2>
    <?php 
    // fetch category from categories table using category_id of post
    $category_id = $id;
    $category_query = "SELECT * FROM categories WHERE id=$id";
    $category_result = mysqli_query($connection, $category_query);
    $category = mysqli_fetch_assoc($category_result);
    echo $category['title']
    ?>
    </h2> 
</header>
    <!-- =========== END OF CATEGORY TITLE =========== -->

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
            <a href="<?= ROOT_URL ?>category.php?id=<?= $category['id'] ?>" class="category_button"><?= $category['title'] ?></a>
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