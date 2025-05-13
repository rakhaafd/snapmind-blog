<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_POST['delete'])) {
   $p_id = filter_var($_POST['post_id'], FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT image FROM `posts` WHERE id = ?");
   $delete_image->execute([$p_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if ($fetch_delete_image['image']) {
      unlink('../public/upload/' . $fetch_delete_image['image']);
   }
   $conn->prepare("DELETE FROM `posts` WHERE id = ?")->execute([$p_id]);
   $conn->prepare("DELETE FROM `comments` WHERE post_id = ?")->execute([$p_id]);
   $message[] = 'post deleted successfully!';
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
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="show-posts">
   <h1 class="heading">Your Posts</h1>
   <div class="box-container">
      <?php
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
      $select_posts->execute([$admin_id]);
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
      <form method="post" class="box">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <?php if ($fetch_posts['image']) { ?>
         <img src="../public/upload/<?= htmlspecialchars($fetch_posts['image']); ?>" class="image" alt="">
         <?php } ?>
         <div class="status" style="background-color: <?= $fetch_posts['status'] == 'active' ? 'limegreen' : 'coral'; ?>;">
            <?= $fetch_posts['status']; ?>
         </div>
         <div class="title"><?= htmlspecialchars($fetch_posts['title']); ?></div>
         <div class="posts-content"><?= htmlspecialchars($fetch_posts['content']); ?></div>
         <div class="icons">
            <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
            <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
         </div>
         <div class="flex-btn">
            <a href="edit_post.php?id=<?= $post_id; ?>" class="option-btn">Edit</a>
            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('delete this post?');">Delete</button>
         </div>
         <a href="read_post.php?post_id=<?= $post_id; ?>" class="btn">View Post</a>
      </form>
      <?php
         }
      } else {
         echo '<p class="empty">no posts added yet! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">Add Post</a></p>';
      }
      ?>
   </div>
</section>

<script src="../assets/js/admin_script.js"></script>
</body>
</html>