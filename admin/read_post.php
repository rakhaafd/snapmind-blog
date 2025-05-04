<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

$get_id = $_GET['post_id'];

if (isset($_POST['delete'])) {
   $p_id = filter_var($_POST['post_id'], FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT image FROM `posts` WHERE id = ?");
   $delete_image->execute([$p_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if ($fetch_delete_image['image']) {
      unlink('../public/img/' . $fetch_delete_image['image']);
   }
   $conn->prepare("DELETE FROM `posts` WHERE id = ?")->execute([$p_id]);
   $conn->prepare("DELETE FROM `comments` WHERE post_id = ?")->execute([$p_id]);
   header('location:view_posts.php');
   exit;
}

if (isset($_POST['delete_comment'])) {
   $comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_STRING);
   $conn->prepare("DELETE FROM `comments` WHERE id = ?")->execute([$comment_id]);
   $message[] = 'comment deleted!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Posts</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="read-post">
   <?php
   $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND id = ?");
   $select_posts->execute([$admin_id, $get_id]);
   if ($select_posts->rowCount() > 0) {
      while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
         $post_id = $fetch_posts['id'];
         $count_post_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE post_id = ?");
         $count_post_comments->execute([$post_id]);
         $total_post_comments = $count_post_comments->fetchColumn();
         $count_post_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE post_id = ?");
         $count_post_likes->execute([$post_id]);
         $total_post_likes = $count_post_likes->fetchColumn();
   ?>
   <form method="post">
      <input type="hidden" name="post_id" value="<?= $post_id; ?>">
      <div class="status" style="background-color: <?= $fetch_posts['status'] == 'active' ? 'limegreen' : 'coral'; ?>;">
         <?= $fetch_posts['status']; ?>
      </div>
      <?php if ($fetch_posts['image']) { ?>
      <img src="../public/img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="image" alt="">
      <?php } ?>
      <div class="title"><?= htmlspecialchars($fetch_posts['title']); ?></div>
      <div class="content"><?= htmlspecialchars($fetch_posts['content']); ?></div>
      <div class="icons">
         <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
         <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
      </div>
      <div class="flex-btn">
         <a href="edit_post.php?id=<?= $post_id; ?>" class="inline-option-btn">Edit</a>
         <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('delete this post?');">Delete</button>
         <a href="view_posts.php" class="inline-option-btn">Go Back</a>
      </div>
   </form>
   <?php
      }
   } else {
      echo '<p class="empty">no posts added yet! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">Add Post</a></p>';
   }
   ?>
</section>

<section class="comments" style="padding-top: 0;">
   <p class="comment-title">Post Comments</p>
   <div class="box-container">
      <?php
      $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
      $select_comments->execute([$get_id]);
      if ($select_comments->rowCount() > 0) {
         while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box">
         <div class="user">
            <i class="fas fa-user"></i>
            <div class="user-info">
               <span><?= htmlspecialchars($fetch_comments['user_name']); ?></span>
               <div><?= $fetch_comments['date']; ?></div>
            </div>
         </div>
         <div class="text"><?= htmlspecialchars($fetch_comments['comment']); ?></div>
         <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">Delete Comment</button>
         </form>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
   </div>
</section>

<script src="../assets/js/admin_script.js"></script>
</body>
</html>