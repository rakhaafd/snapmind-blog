<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_POST['delete_comment'])) {
   $comment_id = filter_var($_POST['comment_id'], FILTER_SANITIZE_STRING);
   $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
   $delete_comment->execute([$comment_id]);
   $message[] = 'Comment Deleted!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Post Comments</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/admin_style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="comments">
   <h1 class="heading">Post Comments</h1>
   <p class="comment-title">Post Comments</p>
   <div class="box-container">
      <?php
      $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
      $select_comments->execute([$admin_id]);
      if ($select_comments->rowCount() > 0) {
         while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
            $select_posts->execute([$fetch_comments['post_id']]);
            while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="post-title">from: <span><?= htmlspecialchars($fetch_posts['title']); ?></span> <a href="read_post.php?post_id=<?= $fetch_posts['id']; ?>">view post</a></div>
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
         }
      } else {
         echo '<p class="empty">No Comments Added Yet!</p>';
      }
      ?>
   </div>
</section>

<script src="../assets/js/admin_script.js"></script>
</body>
</html>