<?php
if (isset($_POST['add_to_cart'])) {
   if (empty($user_id)) {
      header('location:login.php');
      exit;
   }

   $pid = filter_var($_POST['pid'], FILTER_SANITIZE_STRING);
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
   $image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
   $qty = filter_var($_POST['qty'], FILTER_SANITIZE_STRING);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND name = ?");
   $check_cart_numbers->execute([$user_id, $name]);

   if ($check_cart_numbers->rowCount() > 0) {
      $message[] = 'already added to cart!';
   } else {
      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
      $message[] = 'added to cart!';
   }
}
?>