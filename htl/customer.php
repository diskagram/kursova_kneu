<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<?php
session_start();
$is_user_auth = $_SESSION['loggedin'];
$user_role = $_SESSION['user_role'];
if ($is_user_auth == 0 or $user_role != 'single') header("Location: index.php");
require_once 'connection.php';
$link = mysqli_connect($db_host, $db_user, $db_password, $db_db)
or die("Помилка " . mysqli_error($link));

////////////


echo "<h>Забронювати номер для себе</h>";


echo "
<form method = 'post'>
    
      <p> 
         <label for='full_name'>full_name: </label>
         <input type='text' name='full_name'>
      </p>  
      <p> 
         <label for='email'>email: </label>
         <input type='text' name='email'>
      </p> 
      <p> 
         <label for='phone'>phone: </label>
         <input type='text' name='phone'>
      </p> 
      
      <p>
         <label for='time_from'>time_from: </label> 
         <input type='date' name='time_from'>
      </p>  
      <p> 
         <label for='time_to'>time_to: </label>
         <input type='date' name='time_to'>
      </p>  
      
       <p> <input type='submit' value='contact me'> </p>  
      
      
</form>
 ";


if (isset($_POST['full_name'])
    && isset($_POST['email'])
    && isset($_POST['phone'])
    && isset($_POST['time_from'])
    && isset($_POST['time_to'])
) {
    $full_name = htmlentities(mysqli_real_escape_string($link, $_POST['full_name']));
    $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
    $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());
    $email = htmlentities(mysqli_real_escape_string($link, $_POST['email']));
    $phone = htmlentities(mysqli_real_escape_string($link, $_POST['phone']));


    echo "<p>Thanks for order, but our service is not working now :(</p>";


}


mysqli_close($link);

?>


</body>
</html>