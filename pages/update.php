<?php
include '../components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
   $user_id = '';
   header('location:home.php');
   exit;
}
$user_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

   if (!empty($name)) {
      $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?")->execute([$name, $user_id]);
   }

   if (!empty($email)) {
      $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND id != ?");
      $select_email->execute([$email, $user_id]);
      if ($select_email->rowCount() > 0) {
         $message[] = 'email already taken!';
      } else {
         $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?")->execute([$email, $user_id]);
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
   $select_prev_pass->execute([$user_id]);
   $prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC)['password'];
   $old_pass = filter_var(sha1($_POST['old_pass']), FILTER_SANITIZE_STRING);
   $new_pass = filter_var(sha1($_POST['new_pass']), FILTER_SANITIZE_STRING);
   $confirm_pass = filter_var(sha1($_POST['confirm_pass']), FILTER_SANITIZE_STRING);

   if ($old_pass !== $empty_pass) {
      if ($old_pass !== $prev_pass) {
         $message[] = 'old password not matched!';
      } elseif ($new_pass !== $confirm_pass) {
         $message[] = 'confirm password not matched!';
      } elseif ($new_pass === $empty_pass) {
         $message[] = 'please enter a new password!';
      } else {
         $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?")->execute([$confirm_pass, $user_id]);
         $message[] = 'password updated successfully!';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
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

<section class="form-container">
   <form action="" method="post">
      <h3>Update Profile</h3>
      <input type="text" name="name" placeholder="<?= htmlspecialchars($fetch_profile['name']); ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= htmlspecialchars($fetch_profile['email']); ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>
</section>

<?php include '../components/footer.php'; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>