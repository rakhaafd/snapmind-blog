<?php
include '../components/connect.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

include '../components/like_post.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Category</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/style.css">
   <link rel="icon" type="image/png" href="../public/logo/snapmind.png">

</head>
<?php include '../components/user_header.php'; ?>

<body>
<section class="categories">
   <h1 class="heading">Post Categories</h1>
   <div class="box-container">
      <div class="box"><span>01</span><a href="category.php?category=nature">Nature</a></div>
      <div class="box"><span>02</span><a href="category.php?category=education">Education</a></div>
      <div class="box"><span>03</span><a href="category.php?category=pets and animals">Pets and Animals</a></div>
      <div class="box"><span>04</span><a href="category.php?category=technology">Technology</a></div>
      <div class="box"><span>05</span><a href="category.php?category=fashion">Fashion</a></div>
      <div class="box"><span>06</span><a href="category.php?category=entertainment">Entertainment</a></div>
      <div class="box"><span>07</span><a href="category.php?category=movies">Movies</a></div>
      <div class="box"><span>08</span><a href="category.php?category=gaming">Gaming</a></div>
      <div class="box"><span>09</span><a href="category.php?category=music">Music</a></div>
      <div class="box"><span>10</span><a href="category.php?category=sports">Sports</a></div>
      <div class="box"><span>11</span><a href="category.php?category=news">News</a></div>
      <div class="box"><span>12</span><a href="category.php?category=travel">Travel</a></div>
      <div class="box"><span>13</span><a href="category.php?category=comedy">Comedy</a></div>
      <div class="box"><span>14</span><a href="category.php?category=design and development">Design and Development</a></div>
      <div class="box"><span>15</span><a href="category.php?category=food and drinks">Food and Drinks</a></div>
      <div class="box"><span>16</span><a href="category.php?category=lifestyle">Lifestyle</a></div>
      <div class="box"><span>17</span><a href="category.php?category=health and fitness">Health and Fitness</a></div>
      <div class="box"><span>18</span><a href="category.php?category=business">Business</a></div>
      <div class="box"><span>19</span><a href="category.php?category=shopping">Shopping</a></div>
      <div class="box"><span>20</span><a href="category.php?category=animations">Animations</a></div>
   </div>
</section>

<?php include '../components/footer.php'; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>