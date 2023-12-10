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

echo "<h>Отримати дані про рентабельність номерів з певними характеристиками: співвідношення про обсяг продажів номерів до накладних витрат за вказаний період.</h>";
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
          <input type='submit' value='show room profit'> </p>  
</form>
 ";


if (isset($_POST['hotel'])
    && isset($_POST['persons_room'])
    && isset($_POST['room_class'])
) {
    $hotel = htmlentities(mysqli_real_escape_string($link, $_POST['hotel']));
    $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
    $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());
    $pers_in_room = htmlentities(mysqli_real_escape_string($link, $_POST['persons_room']));
    $room_class = htmlentities(mysqli_real_escape_string($link, $_POST['room_class']));



    $query =
        "select sum(revenue_usd) , sum(cost_usd) , sum(revenue_usd) - sum(cost_usd),avg(price_usd), sum(rented_times)
    from hotel.v_room_pnl 
        where  building_id = $hotel and 
               $pers_in_room = capacity and
               '$room_class' = room_class  
               ";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<thead> <tr>";
    echo " <th>revenue</th>
       <th>cost</th>
       <th>profit</th>
       <th>current avg price per 1 room</th>
       <th>rented_times</th>";
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