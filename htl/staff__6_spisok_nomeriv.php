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

echo "<h>6.	Отримати список зайнятих зараз номерів, які звільняються до зазначеного терміну </h>";
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
         <label for='time_to'>the client must leave before - </label>
         <input type='date' name='time_to'>
      </p>  
 
        <p> <input type='submit' value='show next free time'> </p>  
</form>
 ";

if (isset($_POST['hotel'])
    && isset($_POST['time_to'])
) {
    $hotel = htmlentities(mysqli_real_escape_string($link, $_POST['hotel']));
    $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());


    $query = " 
     select room_id,
       capacity,
       room_class,
       price_usd,
       storey,
       building_name,
       stars,
       reception_phone_number,
       room_next_free_time(room_id)
from hotel.rooms
         left join hotel.buildings b on rooms.building_id = b.building_id

where is_room_empty(room_id,
                    current_timestamp,
                    current_timestamp) = 0
      and b.building_id = $hotel
and  room_next_free_time(room_id) < date('$time_to')
     ";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<thead> <tr>";
    echo "
<th>room_id</th>
<th>capacity</th>
<th>room_class</th>
<th>price_usd</th>
<th>storey</th>
<th>building_name</th>
<th>stars</th>
<th>reception phone number</th>
<th>next time room free</th>";
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