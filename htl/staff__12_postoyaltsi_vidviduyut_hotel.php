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

echo "<h>Отримати відомості про постояльців, які більше за всіх відвідують готель по всім корпусам готелів, по певним будівлям.  </h>";
$query = "SELECT building_id, building_name  FROM hotel.buildings";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='hotel'>hotel: </label> 
   <select name='hotel' >
   <option value='all'>all hotels leader</option>
   
   ";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[1]</option>";
}
echo " 
   </select> 
        <p> <input type='submit' value='find_free_rooms'> </p>  
</form>
 ";

if (isset($_POST['hotel'])

) {
    $hotel = htmlentities(mysqli_real_escape_string($link, $_POST['hotel']));


    if ($hotel != 'all') {

        $query = "
         
         select building_name,
       customer_full_name, count(*)
from hotel.room_orders ro
    left join hotel.rooms r on r.room_id = ro.room_id
left join hotel.buildings b on  b.building_id = r.building_id
where b.building_id = $hotel
group by 1, 2
order by 3 desc
         
         ";

    } else {

        $query = "
         select  
       'all hotels', customer_full_name, count(*)
from hotel.room_orders ro
    left join hotel.rooms r on r.room_id = ro.room_id
left join hotel.buildings b on  b.building_id = r.building_id
group by 1,2
order by 3 desc
         ";
    }

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<thead> <tr>";
    echo "<th>hotel</th><th>customer name</th><th>order room, count </th>";
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