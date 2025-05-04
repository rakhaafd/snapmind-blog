<?php
include '../components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
   $user_id = '';
   header('location:home.php');
   exit;
}
$user_id = $_SESSION['user_id'];

if (isset($_POST['edit_comment'])) {
   $edit_comment_id = filter_var($_POST['edit_comment_id'], FILTER_SANITIZE_STRING);
   $comment_edit_box = filter_var($_POST['comment_edit_box'], FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
   $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

   if ($verify_comment->rowCount() > 0) {
      $message[] = 'comment already added!';
   } else {
      $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?")->execute([$comment_edit_box, $edit_comment_id]);
      $message[] = 'your comment edited successfully!';
   }
}

if (isset($_POST['delete_comment'])) {
   $delete_comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_STRING);
   $conn->prepare("DELETE FROM `comments` WHERE id = ?")->execute([$delete_comment_id]);
   $message[] = 'comment deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Comments</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../components/user_header.php'; ?>

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
      <button type="submit" class="inline-btn" name="edit_comment">Edit Comment</button>
      <div class="inline-option-btn" onclick="window.location.href='user_comments.php';">Cancel Edit</div>
   </form>
</section>
<?php } ?>

<section class="comments-container">
   <h1 class="heading">Your Comments</h1>
   <p class="comment-title">Your Comments on the Posts</p>
   <div class="user-comments-container">
      <?php
      $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
      $select_comments->execute([$user_id]);
      if ($select_comments->rowCount() > 0) {
         while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="show-comments">
         <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
         $select_posts->execute([$fetch_comments['post_id']]);
         while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <div class="post-title">from: <span><?= htmlspecialchars($fetch_posts['title']); ?></span> <a href="view_post.php?post_id=<?= $fetch_posts['id']; ?>">view post</a></div>
         <?php } ?>
         <div class="comment-box"><?= htmlspecialchars($fetch_comments['comment']); ?></div>
         <form action="" method="post">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">Edit Comment</button>
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

<?php include '../components/footer.php'; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>