<?php
include '../components/connect.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$get_id = $_GET['post_id'];

if (isset($_POST['add_comment'])) {
   $admin_id = filter_var($_POST['admin_id'], FILTER_SANITIZE_STRING);
   $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
   $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? AND admin_id = ? AND user_id = ? AND user_name = ? AND comment = ?");
   $verify_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);

   if ($verify_comment->rowCount() > 0) {
      $message[] = 'comment already added!';
   } else {
      $insert_comment = $conn->prepare("INSERT INTO `comments`(post_id, admin_id, user_id, user_name, comment) VALUES(?,?,?,?,?)");
      $insert_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);
      $message[] = 'new comment added!';
   }
}

if (isset($_POST['edit_comment'])) {
   $edit_comment_id = filter_var($_POST['edit_comment_id'], FILTER_SANITIZE_STRING);
   $comment_edit_box = filter_var($_POST['comment_edit_box'], FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
   $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

   if ($verify_comment->rowCount() > 0) {
      $message[] = 'comment already added!';
   } else {
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$comment_edit_box, $edit_comment_id]);
      $message[] = 'your comment edited successfully!';
   }
}

if (isset($_POST['delete_comment'])) {
   $delete_comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_STRING);
   $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
   $delete_comment->execute([$delete_comment_id]);
   $message[] = 'comment deleted successfully!';
}

include '../components/like_post.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>view post</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php 
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
include '../components/user_header.php'; 
?>

<?php if (isset($_POST['open_edit_box'])) {
   $comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_STRING);
?>
<section class="comment-edit-form">
   <p>edit your comment</p>
   <?php
   $select_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $select_edit_comment->execute([$comment_id]);
   $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="post">
      <input type="hidden" name="edit_comment_id" value="<?= $comment_id; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="please enter your comment"><?= htmlspecialchars($fetch_edit_comment['comment']); ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">edit comment</button>
      <div class="inline-option-btn" onclick="window.location.href='view_post.php?post_id=<?= $get_id; ?>';">cancel edit</div>
   </form>
</section>
<?php } ?>

<section class="posts-container" style="padding-bottom: 0;">
   <div class="box-container">
      <?php
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? AND id = ?");
      $select_posts->execute(['active', $get_id]);
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
         <div class="post-content"><?= htmlspecialchars($fetch_posts['content']); ?></div>
         <div class="icons">
            <div><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></div>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($confirm_likes->rowCount() > 0) { echo 'color:var(--red);'; } ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
         </div>
      </form>
      <?php
         }
      } else {
         echo '<p class="empty">no posts found!</p>';
      }
      ?>
   </div>
</section>

<section class="comments-container">
   <p class="comment-title">Add Comment</p>
   <?php if ($user_id) { 
      $select_admin_id = $conn->prepare("SELECT admin_id FROM `posts` WHERE id = ?");
      $select_admin_id->execute([$get_id]);
      $fetch_admin_id = $select_admin_id->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="post" class="add-comment">
      <input type="hidden" name="admin_id" value="<?= $fetch_admin_id['admin_id']; ?>">
      <input type="hidden" name="user_name" value="<?= htmlspecialchars($fetch_profile['name']); ?>">
      <p class="user"><i class="fas fa-user"></i><a href="update.php"><?= htmlspecialchars($fetch_profile['name']); ?></a></p>
      <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="write your comment" required></textarea>
      <input type="submit" value="add comment" class="inline-btn" name="add_comment">
   </form>
   <?php } else { ?>
   <div class="add-comment">
      <p>Please Login to add or edit your comment</p>
      <a href="login.php" class="inline-btn">Login Now</a>
   </div>
   <?php } ?>
   <p class="comment-title">Post Comments</p>
   <div class="user-comments-container">
      <?php
      $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
      $select_comments->execute([$get_id]);
      if ($select_comments->rowCount() > 0) {
         while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="show-comments" style="<?php if ($fetch_comments['user_id'] == $user_id) { echo 'order:-1;'; } ?>">
         <div class="comment-user">
            <i class="fas fa-user"></i>
            <div>
               <span><?= htmlspecialchars($fetch_comments['user_name']); ?></span>
               <div><?= $fetch_comments['date']; ?></div>
            </div>
         </div>
         <div class="comment-box" style="<?php if ($fetch_comments['user_id'] == $user_id) { echo 'color:var(--white); background:var(--black);'; } ?>"><?= htmlspecialchars($fetch_comments['comment']); ?></div>
         <?php if ($fetch_comments['user_id'] == $user_id) { ?>
         <form action="" method="post">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">Edit Comment</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">Delete Comment</button>
         </form>
         <?php } ?>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
   </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>