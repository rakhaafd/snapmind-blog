<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/admin_style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="dashboard">
   <h1 class="heading">Dashboard</h1>
   <div class="box-container">
      <div class="box">
         <h3>Welcome! 🚀</h3>
         <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
         <a href="update_profile.php" class="btn">Update Profile</a>
      </div>

      <div class="box">
         <?php
         $select_posts = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE admin_id = ?");
         $select_posts->execute([$admin_id]);
         $numbers_of_posts = $select_posts->fetchColumn();
         ?>
         <h3><?= $numbers_of_posts; ?></h3>
         <p>Posts Added</p>
         <a href="add_posts.php" class="btn">Add New Post</a>
      </div>

      <div class="box">
         <?php
         $select_active_posts = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE admin_id = ? AND status = ?");
         $select_active_posts->execute([$admin_id, 'active']);
         $numbers_of_active_posts = $select_active_posts->fetchColumn();
         ?>
         <h3><?= $numbers_of_active_posts; ?></h3>
         <p>active posts</p>
         <a href="view_posts.php" class="btn">See Posts</a>
      </div>

      <div class="box">
         <?php
         $select_deactive_posts = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE admin_id = ? AND status = ?");
         $select_deactive_posts->execute([$admin_id, 'deactive']);
         $numbers_of_deactive_posts = $select_deactive_posts->fetchColumn();
         ?>
         <h3><?= $numbers_of_deactive_posts; ?></h3>
         <p>deactive posts</p>
         <a href="view_posts.php" class="btn">See Posts</a>
      </div>

      <div class="box">
         <?php
         $select_users = $conn->prepare("SELECT COUNT(*) FROM `users`");
         $select_users->execute();
         $numbers_of_users = $select_users->fetchColumn();
         ?>
         <h3><?= $numbers_of_users; ?></h3>
         <p>Users Account</p>
         <a href="users_accounts.php" class="btn">See Users</a>
      </div>

      <div class="box">
         <?php
         $select_admins = $conn->prepare("SELECT COUNT(*) FROM `admin`");
         $select_admins->execute();
         $numbers_of_admins = $select_admins->fetchColumn();
         ?>
         <h3><?= $numbers_of_admins; ?></h3>
         <p>Admins Account</p>
         <a href="admin_accounts.php" class="btn">See Admins</a>
      </div>

      <div class="box">
         <?php
         $select_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE admin_id = ?");
         $select_comments->execute([$admin_id]);
         $numbers_of_comments = $select_comments->fetchColumn();
         ?>
         <h3><?= $numbers_of_comments; ?></h3>
         <p>Comments Added</p>
         <a href="comments.php" class="btn">See Comments</a>
      </div>

      <div class="box">
         <?php
         $select_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE admin_id = ?");
         $select_likes->execute([$admin_id]);
         $numbers_of_likes = $select_likes->fetchColumn();
         ?>
         <h3><?= $numbers_of_likes; ?></h3>
         <p>Total Likes</p>
         <a href="view_posts.php" class="btn">See Posts</a>
      </div>
   </div>
</section>

<script src="../assets/js/admin_script.js"></script>
</body>
</html>