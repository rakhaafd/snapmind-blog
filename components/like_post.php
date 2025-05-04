<?php
if (isset($_POST['like_post'])) {
   if (empty($user_id)) {
      $message[] = 'please login first!';
   } else {
      $post_id = filter_var($_POST['post_id'], FILTER_SANITIZE_STRING);
      $admin_id = filter_var($_POST['admin_id'], FILTER_SANITIZE_STRING);

      $select_post_like = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ? AND user_id = ?");
      $select_post_like->execute([$post_id, $user_id]);

      if ($select_post_like->rowCount() > 0) {
         $conn->prepare("DELETE FROM `likes` WHERE post_id = ? AND user_id = ?")->execute([$post_id, $user_id]);
         $message[] = 'removed from likes';
      } else {
         $conn->prepare("INSERT INTO `likes`(user_id, post_id, admin_id) VALUES(?,?,?)")->execute([$user_id, $post_id, $admin_id]);
         $message[] = 'added to likes';
      }
   }
}
?>