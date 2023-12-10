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
if ($is_user_auth == 0) header("Location: index.php");
require_once 'connection.php';
$link = mysqli_connect($db_host, $db_user, $db_password, $db_db)
or die("Помилка " . mysqli_error($link));

////////////

echo "<h>Отримати відомості про нових клієнтів за вказаний період.  </h>";

echo "
<form method = 'post'>
      <p>
         <label for='time_from'>time_from: </label> 
         <input type='date' name='time_from'>
      </p>  
      <p> 
         <label for='time_to'>time_to: </label>
         <input type='date' name='time_to'>
      </p>  
 
        <p> <input type='submit' value='show new clients'> </p>  
</form>
 ";

if (

    isset($_POST['time_from'])
    && isset($_POST['time_to'])
) {

    $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
    $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());


    $query = " 
    select * from hotel.customers
    where is_new_client(passport_id, '$time_from', '$time_to')  = 1
    ";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<thead> <tr>";
    echo "<th>passport_id</th><th>full_name</th><th>email</th><th>phone</th>";
    echo "</tr></thead>";
    echo "<tbody>";
    for ($i = 0; $i < $table_data_rows_number; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";
        for ($j = 0; $j < $table_data_columns_number; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";


    mysqli_close($link);
}
?>


<p><a href="staff.php">GO TO STAFF PAGE</a></p>
</body>
</html>