<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_POST['delete'])) {
   $delete_image = $conn->prepare("SELECT image FROM `posts` WHERE admin_id = ?");
   $delete_image->execute([$admin_id]);
   while ($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)) {
      if ($fetch_delete_image['image']) {
         unlink('../public/upload/' . $fetch_delete_image['image']);
      }
   }

   $conn->prepare("DELETE FROM `posts` WHERE admin_id = ?")->execute([$admin_id]);
   $conn->prepare("DELETE FROM `likes` WHERE admin_id = ?")->execute([$admin_id]);
   $conn->prepare("DELETE FROM `comments` WHERE admin_id = ?")->execute([$admin_id]);
   $conn->prepare("DELETE FROM `admin` WHERE id = ?")->execute([$admin_id]);
   header('location:../components/admin_logout.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admins Accounts</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/admin_style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">
</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="accounts">
   <h1 class="heading">Admins Account</h1>
   <div class="box-container">
      <div class="box" style="order: -2;">
         <p>Register New Admin</p>
         <a href="register_admin.php" class="option-btn" style="margin-bottom: .5rem;">Register</a>
      </div>

      <?php
      $select_account = $conn->prepare("SELECT * FROM `admin`");
      $select_account->execute();
      if ($select_account->rowCount() > 0) {
         while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
            $count_admin_posts = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE admin_id = ?");
            $count_admin_posts->execute([$fetch_accounts['id']]);
            $total_admin_posts = $count_admin_posts->fetchColumn();
      ?>
      <div class="box" style="order: <?= $fetch_accounts['id'] == $admin_id ? '-1' : '0'; ?>;">
         <p>Admin ID: <span><?= htmlspecialchars($fetch_accounts['id']); ?></span></p>
         <p>Username: <span><?= htmlspecialchars($fetch_accounts['name']); ?></span></p>
         <p>Total Posts: <span><?= $total_admin_posts; ?></span></p>
         <div class="flex-btn">
            <?php if ($fetch_accounts['id'] == $admin_id) { ?>
               <a href="update_profile.php" class="option-btn" style="margin-bottom: .5rem;">update</a>
               <form action="" method="POST">
                  <input type="hidden" name="post_id" value="<?= $fetch_accounts['id']; ?>">
                  <button type="submit" name="delete" onclick="return confirm('delete the account?');" class="delete-btn" style="margin-bottom: .5rem;">delete</button>
               </form>
            <?php } ?>
         </div>
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