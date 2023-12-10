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

echo "<h>2.	Отримати перелік і загальне число постояльців, що заселяли в номери із зазначеними характеристиками за певний період. </h>";
$query = "SELECT building_id, building_name  FROM hotel.buildings";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='hotel'>hotel: </label> 
   <select name='hotel' >";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[1]</option>";
}
echo " 
   </select> 
      <p>
         <label for='time_from'>time_from: </label> 
         <input type='date' name='time_from'>
      </p>  
      <p> 
         <label for='time_to'>time_to: </label>
         <input type='date' name='time_to'>
      </p>  
               
      <p> 
         <label for='persons_room'>persons in room: </label> 
         <select name='persons_room'>
             <option value=1>1</option>
             <option value=2>2</option>
             <option value=3>3</option>
             <option value=4>4</option>
         </select>
      </p>
        <p> 
          <label for='room_class'>room class: </label> 
          <select name='room_class' >
              <option value='economic'>economic</option>
              <option value='middle'>middle</option>
              <option value='luxurious'>luxurious</option>
          </select>
        </p> 

        
        <p> <input type='submit' value='find_free_rooms'> </p>  
</form>
 ";

if (isset($_POST['hotel'])
    && isset($_POST['time_from'])
    && isset($_POST['time_to'])
    && isset($_POST['persons_room'])
    && isset($_POST['room_class'])
) {
    $hotel = htmlentities(mysqli_real_escape_string($link, $_POST['hotel']));
    $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
    $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());
    $pers_in_room = htmlentities(mysqli_real_escape_string($link, $_POST['persons_room']));
    $room_class = htmlentities(mysqli_real_escape_string($link, $_POST['room_class']));


    $query = " call show_who_in_the_rooms_class($hotel,$pers_in_room, '$room_class', '$time_from', '$time_to') ";
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