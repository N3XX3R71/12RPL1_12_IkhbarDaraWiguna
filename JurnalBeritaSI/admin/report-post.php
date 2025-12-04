<?php
    require 'config/database.php';

    // pagination
    $per_page = 6;
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($current_page - 1) * $per_page;

    // Tangkap parameter sort_option
    $sort_option = $_GET['sort_option'] ?? 'all';

    // fetch current user's posts from database
    $current_user_id = $_SESSION['user-id'];

    $sql_order_by = "ORDER BY p.views DESC"; // Default
    $limit_clause = "LIMIT $per_page OFFSET $offset";

    switch ($sort_option) {
        case 'views_desc':
            $sql_order_by = "ORDER BY p.views DESC";
            $limit_clause = "LIMIT $per_page OFFSET $offset"; // Gunakan per_page
            break;
        case 'views_asc':
            $sql_order_by = "ORDER BY p.views ASC";
            $limit_clause = "LIMIT $per_page OFFSET $offset"; // Gunakan per_page
            break;
        case 'rating_desc':
            $sql_order_by = "ORDER BY average_rating DESC";
            $limit_clause = "LIMIT $per_page OFFSET $offset"; // Gunakan per_page
            break;
        case 'rating_asc':
            $sql_order_by = "ORDER BY average_rating ASC";
            $limit_clause = "LIMIT $per_page OFFSET $offset"; // Gunakan per_page
            break;
        case 'all':
        default:
            // Default limit dan offset untuk semua data dengan pagination
            $limit_clause = "LIMIT $per_page OFFSET $offset";
            $sql_order_by = "ORDER BY p.views DESC";
            break;
    }

    $query = "SELECT p.id, p.title, p.category_id, p.views, COALESCE(AVG(r.rating), 0) as average_rating 
              FROM post p
              LEFT JOIN post_ratings r ON p.id = r.post_id
              WHERE p.author_id=$current_user_id 
              GROUP BY p.id 
              $sql_order_by $limit_clause"; 
    $posts = mysqli_query($connection, $query);

    // fetch total posts for pagination
    $total_posts_query_base = "SELECT COUNT(*) FROM (
                                    SELECT p.id
                                    FROM post p
                                    WHERE p.author_id=$current_user_id
                                    GROUP BY p.id
                                  ) AS total_filtered_posts";

    // Paginasi selalu aktif kecuali jika filter spesifik membatasi jumlah hasil
    $total_posts_result = mysqli_query($connection, $total_posts_query_base);
    $total_posts = mysqli_fetch_row($total_posts_result)[0];
    $total_pages = ceil($total_posts / $per_page);

    // fetch user from database
    $query = "SELECT * FROM users WHERE id = $current_user_id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);
    $avatar = $user['avatar'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css" />
    <!-- ICONSCOUT CDN -->
    <link
      rel="stylesheet"
      href="https://unicons.iconscout.com/release/v4.0.8/css/line.css"
    />
  </head>
  <body>
    <div class="sidebar">
      <div class="profile-section">
        <img src="<?= ROOT_URL ?>images/<?= $avatar ?>" alt="Profile" class="profile-img" />
        <div class="profile-name"><?php echo $user['username'] ?></div>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="add-post.php"
            ><i class="uil uil-pen"></i> <span>Add Post</span></a>
        </li>
        <li>
          <a href="index.php"
            ><i class="uil uil-postcard"></i> <span>Manage Post</span></a>
        </li>
        <li>
          <a href="add-category.php"
            ><i class="uil uil-pen"></i> <span>Add Category</span></a>
        </li>
        <li>
          <a href="manage-categories.php"
            ><i class="uil uil-document-layout-left"></i>
            <span>Manage Category</span></a>
        </li>
        <li>
          <a href="add-user.php"
            ><i class="uil uil-pen"></i> <span>Add User</span></a>
        </li>
        <li>
          <a href="manage-users.php"
            ><i class="uil uil-users-alt"></i>
            <span>Manage Users</span></a>
        </li>
        <li>
          <a href="manage-comment.php"
            ><i class="uil uil-comment-alt-notes"></i>
            <span>Manage Comment</span></a>
        </li>
        <li>
          <a href="report-post.php" class="active"
            ><i class="uil uil-file-alt"></i>
            <span>Report Post</span></a>
        </li>
        <li>
          <a href="<?= ROOT_URL ?>logout.php"><i class="uil uil-signout"></i> <span>Logout</span></a>
        </li>
      </ul>
    </div>

    <div class="manage-post-section">
      <h2>Report Post</h2>

      <form action="<?= ROOT_URL ?>admin/report-post.php" method="GET" class="filter-form">
        <div class="form-group">
          <label for="sort_option">Filter & Urutkan:</label>
          <select id="sort_option" name="sort_option" class="form-control">
            <option value="all" <?= (($_GET['sort_option'] ?? '') == 'all') ? 'selected' : '' ?>>Semua</option>
            <option value="views_desc" <?= (($_GET['sort_option'] ?? '') == 'views_desc') ? 'selected' : '' ?>>Tertinggi Views</option>
            <option value="views_asc" <?= (($_GET['sort_option'] ?? '') == 'views_asc') ? 'selected' : '' ?>>Terendah Views</option>
            <option value="rating_desc" <?= (($_GET['sort_option'] ?? '') == 'rating_desc') ? 'selected' : '' ?>>Tertinggi Rating</option>
            <option value="rating_asc" <?= (($_GET['sort_option'] ?? '') == 'rating_asc') ? 'selected' : '' ?>>Terendah Rating</option>
          </select>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; margin-top: 10px;">
          <button type="submit" class="btn primary">Terapkan Filter</button>
          <a href="#" id="print-report-btn" class="btn danger">Cetak Laporan</a>
        </div>
      </form>
      <br>

      <?php if(isset($_SESSION['add-post-success'])) : ?>
        <div class="alert_message success">
          <?= $_SESSION['add-post-success']; ?>
          <?php unset($_SESSION['add-post-success']); ?>
        </div>
      <?php elseif(isset($_SESSION['edit-post-success'])) : ?>
        <div class="alert_message success">
          <?= $_SESSION['edit-post-success']; ?>
          <?php unset($_SESSION['edit-post-success']); ?>
        </div>
      <?php elseif(isset($_SESSION['delete-post-success'])) : ?>
        <div class="alert_message success">
          <?= $_SESSION['delete-post-success']; ?>
          <?php unset($_SESSION['delete-post-success']); ?>
        </div>
      <?php elseif(isset($_SESSION['delete-post-error'])) : ?>
        <div class="alert_message error">
          <?= $_SESSION['delete-post-error']; ?>
          <?php unset($_SESSION['delete-post-error']); ?>
        </div>
      <?php endif ?>
      <?php if(mysqli_num_rows($posts) > 0) : ?>
      <table class="user-table">
        <thead>
          <tr>
            <th scope="col">Judul</th>
            <th scope="col">Views</th>
            <th scope="col">Rating</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($post = mysqli_fetch_assoc($posts)) : ?>
            <!-- get category title of each post from categories table -->
            <?php
            $category_id = $post['category_id'];
            $category_query = "SELECT title FROM categories WHERE id=$category_id";
            $category_result = mysqli_query($connection, $category_query);
            $category = mysqli_fetch_assoc($category_result);
            ?>
          <tr>
            <td data-label="Judul">
              <?= $post['title'] ?>
            </td>
            <td data-label="Views">
              <?= $post['views'] ?>
            </td>
            <td data-label="Rating">
              <?= $post['average_rating'] ?>
            </td>
          </tr>
          <?php endwhile ?>
        </tbody>
      </table>
      <?php else : ?>
        <div class="alert_message error"><?= "No posts found" ?></div>
      <?php endif ?>

      <?php if(mysqli_num_rows($posts) > 0) : ?>
      <div class="pagination-wrapper">
        <div aria-label="...">
          <ul class="pagination">
            <?php if ($current_page > 1) : ?>
              <li class="page-item">
                <a href="<?= ROOT_URL ?>admin/report-post.php?page=<?= $current_page - 1 ?>" class="page-link">Previous</a>
              </li>
            <?php endif ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
              <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= ROOT_URL ?>admin/report-post.php?page=<?= $i ?>" <?= ($i == $current_page) ? 'aria-current="page"' : '' ?>><?= $i ?></a>
              </li>
            <?php endfor ?>

            <?php if ($current_page < $total_pages) : ?>
              <li class="page-item">
                <a class="page-link" href="<?= ROOT_URL ?>admin/report-post.php?page=<?= $current_page + 1 ?>">Next</a>
              </li>
            <?php endif ?>
          </ul>
        </div>
      </div>
      <?php endif ?>
    </div>
  </body>
  <script>
    document.getElementById('print-report-btn').addEventListener('click', function(e) {
      e.preventDefault();
      const sortOption = document.getElementById('sort_option').value;

      let queryParams = '';
      if (sortOption) queryParams += `sort_option=${sortOption}`;
      
      window.open(`<?= ROOT_URL ?>admin/report_post_view_rating.php?${queryParams}`, '_blank');
    });
  </script>
</html>
