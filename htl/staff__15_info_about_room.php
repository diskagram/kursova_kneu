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

echo "<h>Отримати відомості про конкретний номер: ким він був зайнятий в певний період.</h>";

$query = "SELECT building_id, building_name  FROM hotel.buildings";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>

   </select> 
      <p>
         <label for='room'>room: </label> 
         <input type='text' name='room'>
      </p>  
      <p> <input type='submit' value='show info'> </p>  
</form>
 ";


if (isset($_POST['room'])) {
    $room = htmlentities(mysqli_real_escape_string($link, $_POST['room']));


    $query =
        "select *
    from hotel.room_orders
        where  room_id = $room
               ";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));


    $query_columns = "
            SELECT    
                COLUMN_NAME
            FROM 
            information_schema.COLUMNS
            WHERE
            TABLE_SCHEMA = 'hotel' and TABLE_NAME  = 'room_orders'
            order by ORDINAL_POSITION
            ";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $columns_labels = mysqli_query($link, $query_columns) or die("Ошибка " . mysqli_error($link));

    if ($result && $columns_labels) {
        $table_data_rows_number = mysqli_num_rows($result);
        $table_data_columns_number = mysqli_num_fields($result);
        $table_header_rows_number = mysqli_num_rows($columns_labels);

        echo "<table class='styled-table' >";
        echo "<caption>room orders:</caption>";
        echo "<thead> <tr>";
        for ($i = 0; $i < $table_header_rows_number; ++$i) {
            $row = mysqli_fetch_row($columns_labels);
            echo "<th scope='col'>$row[0]</th>";
        }
        echo "</tr></thead>";

        echo "<tbody>";
        for ($i = 0; $i < $table_data_rows_number; ++$i) {
            $row = mysqli_fetch_row($result);
            echo "<tr>";
            for ($j = 0; $j < $table_data_columns_number; ++$j) echo "<td>$row[$j]</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        mysqli_free_result($result);
    }


    $query =
        "select room_id,
       capacity, room_class, price_usd, storey, 
       building_name, storeys, stars, reception_phone_number,
       is_room_empty(room_id, current_timestamp, current_timestamp),
       room_next_free_time(room_id)
    from hotel.rooms
    left join hotel.buildings b on b.building_id = rooms.building_id
        where  room_id = $room
               ";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<thead> <tr>";
    echo " <th>room_id</th>
       <th>capacity</th>
       <th>room_class</th>
       <th>price_usd</th>
       <th>storey</th>
       <th>building_name</th>
       <th>storeys</th>
       <th>stars</th>
       <th>reception_phone_number</th>
       <th>is_room_empty</th>
       <th>rooom_next_free_time</th>";
    echo "</tr></thead>";
    echo "<tbody>";
    for ($i = 0; $i < $table_data_rows_number; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";
        for ($j = 0; $j < $table_data_columns_number; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";


}


mysqli_close($link);

?>

<p><a href="staff.php">GO TO STAFF PAGE</a></p>
</body>
</html>