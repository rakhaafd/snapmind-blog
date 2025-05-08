<?php
include '../components/connect.php';

session_start();

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $pass = filter_var(sha1($_POST['pass']), FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);

   if ($select_admin->rowCount() > 0) {
      $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $fetch_admin_id['id'];
      header('location:dashboard.php');
      exit;
   } else {
      $message[] = 'incorrect username or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/admin_style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<body style="padding-left: 0 !important;">
<?php
if (!empty($message)) {
   foreach ($message as $msg) {
      echo '
      <div class="message">
         <span>' . htmlspecialchars($msg) . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<section class="form-container">
   <form action="" method="POST">
      <h3>Login Admin</h3>
      <input type="text" name="name" maxlength="20" required placeholder="enter admin username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="20" required placeholder="enter admin password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="login now" name="submit" class="btn">
   </form>
</section>
</body>
</html>