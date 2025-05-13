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
   <title>Search</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/user_header.php'; ?>

<?php if (isset($_POST['search_box']) || isset($_POST['search_btn'])) { ?>
<section class="posts-container">
   <div class="box-container">
      <?php
      $search_box = $_POST['search_box'];
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE title LIKE ? OR category LIKE ? AND status = ?");
      $select_posts->execute(["%$search_box%", "%$search_box%", 'active']);
      if ($select_posts->rowCount() > 0) {
         while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
            $post_id = $fetch_posts['id'];
            $count_post_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->fetchColumn();
            $count_post_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->fetchColumn();
            $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
            $confirm_likes->execute([$user_id, $post_id]);
      ?>
      <form class="box" method="post">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id']; ?>">
         <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
               <a href="author_posts.php?author=<?= htmlspecialchars($fetch_posts['name']); ?>"><?= htmlspecialchars($fetch_posts['name']); ?></a>
               <div><?= $fetch_posts['date']; ?></div>
            </div>
         </div>
         <?php if ($fetch_posts['image']) { ?>
         <img src="../public/upload/<?= htmlspecialchars($fetch_posts['image']); ?>" class="post-image" alt="">
         <?php } ?>
         <div class="post-title"><?= htmlspecialchars($fetch_posts['title']); ?></div>
         <div class="post-content content-150"><?= htmlspecialchars($fetch_posts['content']); ?></div>
         <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">read more</a>
         <a href="category.php?category=<?= htmlspecialchars($fetch_posts['category']); ?>" class="post-cat"><i class="fas fa-tag"></i><span><?= htmlspecialchars($fetch_posts['category']); ?></span></a>
         <div class="icons">
            <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($confirm_likes->rowCount() > 0) { echo 'color:var(--red);'; } ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
         </div>
      </form>
      <?php
         }
      } else {
         echo '<p class="empty">no result found!</p>';
      }
      ?>
   </div>
</section>
<?php } else { ?>
<section><p class="empty">Search Something!</p></section>
<?php } ?>

<?php include '../components/footer.php'; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>