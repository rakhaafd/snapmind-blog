<?php
include '../components/connect.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

include '../components/like_post.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Authors</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/user_header.php'; ?>

<section class="authors">
   <h1 class="heading">Authors</h1>
   <div class="box-container">
      <?php
      $select_author = $conn->prepare("SELECT * FROM `admin`");
      $select_author->execute();
      if ($select_author->rowCount() > 0) {
         while ($fetch_authors = $select_author->fetch(PDO::FETCH_ASSOC)) {
            $count_admin_posts = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE admin_id = ? AND status = ?");
            $count_admin_posts->execute([$fetch_authors['id'], 'active']);
            $total_admin_posts = $count_admin_posts->fetchColumn();
            $count_admin_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE admin_id = ?");
            $count_admin_likes->execute([$fetch_authors['id']]);
            $total_admin_likes = $count_admin_likes->fetchColumn();
            $count_admin_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE admin_id = ?");
            $count_admin_comments->execute([$fetch_authors['id']]);
            $total_admin_comments = $count_admin_comments->fetchColumn();
      ?>
      <div class="box">
         <p>Author: <span><?= htmlspecialchars($fetch_authors['name']); ?></span></p>
         <p>Total Posts: <span><?= $total_admin_posts; ?></span></p>
         <p>Posts Likes: <span><?= $total_admin_likes; ?></span></p>
         <p>Posts Comments: <span><?= $total_admin_comments; ?></span></p>
         <a href="author_posts.php?author=<?= htmlspecialchars($fetch_authors['name']); ?>" class="btn">view posts</a>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">no authors found</p>';
      }
      ?>
   </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>