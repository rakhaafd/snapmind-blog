<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   if (!empty($name)) {
      $select_name = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
      $select_name->execute([$name]);
      if ($select_name->rowCount() > 0) {
         $message[] = 'username already taken!';
      } else {
         $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?")->execute([$name, $admin_id]);
      }
   }

   $old_pass = filter_var(sha1($_POST['old_pass']), FILTER_SANITIZE_STRING);
   $new_pass = filter_var(sha1($_POST['new_pass']), FILTER_SANITIZE_STRING);
   $confirm_pass = filter_var(sha1($_POST['confirm_pass']), FILTER_SANITIZE_STRING);
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

   if ($old_pass !== $empty_pass) {
      $select_old_pass = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
      $select_old_pass->execute([$admin_id]);
      $prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC)['password'];

      if ($old_pass !== $prev_pass) {
         $message[] = 'old password not matched!';
      } elseif ($new_pass !== $confirm_pass) {
         $message[] = 'confirm password not matched!';
      } elseif ($new_pass === $empty_pass) {
         $message[] = 'please enter a new password!';
      } else {
         $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?")->execute([$confirm_pass, $admin_id]);
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
   <title>Profile Update</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/admin_style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="form-container">
   <form action="" method="POST">
      <h3>Update Profile</h3>
      <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?= htmlspecialchars($fetch_profile['name']); ?>">
      <input type="password" name="old_pass" maxlength="20" placeholder="enter your old password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" maxlength="20" placeholder="enter your new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" maxlength="20" placeholder="confirm your new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>
</section>

<script src="../assets/js/admin_script.js"></script>
</body>
</html>