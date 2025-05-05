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
   <title>Home</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/user_header.php'; ?>

<section class="home-grid">
   <div class="box-container">
      <div class="box">
         <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$user_id]);
         if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            $count_user_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE user_id = ?");
            $count_user_comments->execute([$user_id]);
            $total_user_comments = $count_user_comments->fetchColumn();
            $count_user_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE user_id = ?");
            $count_user_likes->execute([$user_id]);
            $total_user_likes = $count_user_likes->fetchColumn();
         ?>
         <p><strong>Welcome! 🚀 <span><?= htmlspecialchars($fetch_profile['name']); ?></span></strong></p>
         <p><strong>Total Comments: <span><?= $total_user_comments; ?></span></strong></p>
         <p><strong>Posts Liked: <span><?= $total_user_likes; ?></span></strong></p>
         <a href="update.php" class="btn">Update Profile</a>
         <div class="flex-btn">
            <a href="user_likes.php" class="option-btn">Likes</a>
            <a href="user_comments.php" class="option-btn">Comments</a>
         </div>
         <?php } else { ?>
         <p class="name">Login or Register!</p>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
         <?php } ?>
      </div>
      <div class="box">
         <p><strong>Categories 📚</strong></p>
         <div class="flex-box">
            <a href="category.php?category=nature" class="links">Nature</a>
            <a href="category.php?category=education" class="links">Education</a>
            <a href="category.php?category=business" class="links">Business</a>
            <a href="category.php?category=travel" class="links">Travel</a>
            <a href="category.php?category=news" class="links">News</a>
            <a href="category.php?category=gaming" class="links">Gaming</a>
            <a href="category.php?category=sports" class="links">Sports</a>
            <a href="category.php?category=design" class="links">Design</a>
            <a href="category.php?category=fashion" class="links">Fashion</a>
            <a href="category.php?category=personal" class="links">Personal</a>
            <a href="all_category.php" class="btn">View All</a>
         </div>
      </div>
      <div class="box">
         <p><strong>Authors 👤</strong></p>
         <div class="flex-box">
            <?php
            $select_authors = $conn->prepare("SELECT DISTINCT name FROM `admin` LIMIT 10");
            $select_authors->execute();
            if ($select_authors->rowCount() > 0) {
               while ($fetch_authors = $select_authors->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <a href="author_posts.php?author=<?= htmlspecialchars($fetch_authors['name']); ?>" class="links"><?= htmlspecialchars($fetch_authors['name']); ?></a>
            <?php
               }
            } else {
               echo '<p class="empty">no posts added yet!</p>';
            }
            ?>
            <a href="authors.php" class="btn">View All</a>
         </div>
      </div>
   </div>
</section>

<section class="posts-container">
   <h1 class="heading">Latest Posts 🕜</h1>
   <div class="box-container">
      <?php
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? LIMIT 6");
      $select_posts->execute(['active']);
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
         <img src="../public/img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="post-image" alt="">
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
         echo '<p class="empty">no posts added yet!</p>';
      }
      ?>
   </div>
   <div class="more-btn" style="text-align: center; margin-top:1rem;">
      <a href="posts.php" class="inline-btn">View All Posts</a>
   </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>