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
   <title>Users Accounts</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/admin_style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="accounts">
   <h1 class="heading">Users Account</h1>
   <div class="box-container">
      <?php
      $select_account = $conn->prepare("SELECT * FROM `users`");
      $select_account->execute();
      if ($select_account->rowCount() > 0) {
         while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
            $user_id = $fetch_accounts['id'];
            $count_user_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE user_id = ?");
            $count_user_comments->execute([$user_id]);
            $total_user_comments = $count_user_comments->fetchColumn();
            $count_user_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE user_id = ?");
            $count_user_likes->execute([$user_id]);
            $total_user_likes = $count_user_likes->fetchColumn();
      ?>
      <div class="box">
         <p>users id: <span><?= htmlspecialchars($user_id); ?></span></p>
         <p>username: <span><?= htmlspecialchars($fetch_accounts['name']); ?></span></p>
         <p>total comments: <span><?= $total_user_comments; ?></span></p>
         <p>total likes: <span><?= $total_user_likes; ?></span></p>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">no accounts available</p>';
      }
      ?>
   </div>
</section>

<script src="../assets/js/admin_script.js"></script>
</body>
</html>