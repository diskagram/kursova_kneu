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

echo "<h>Отримати відомості про кількість вільних номерів із зазначеними характеристиками</h>";
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
         <p><label for='show_all_time'>show all rooms, dont see on prev selections </label> 
       <input type='checkbox' name='show_all_time' value='Yes' /></p>  
        <p>
          <input type='submit' value='find_free_rooms'> </p>  
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
    $show_all_time = htmlentities(mysqli_real_escape_string($link, $_POST['show_all_time'])) ?: 'NO';


    if ($show_all_time == 'Yes') {

        $query4 =
            "select count(distinct room_id)
    from hotel.rooms 
               ";
        $result4 = mysqli_query($link, $query4) or die("Ошибка " . mysqli_error($link));
        $row4 = mysqli_fetch_row($result4)[0];


        $query3 =
            "
         select count(distinct room_id)
    from hotel.rooms r

    where is_room_empty(room_id, '$time_from', '$time_to') = 1 
               ";
        $result3 = mysqli_query($link, $query3) or die("Ошибка " . mysqli_error($link));
        $row3 = mysqli_fetch_row($result3)[0];
        echo "all free  rooms: $row3 from $row4";

    } else {

        $query = "select count_rooms_empty($hotel,$pers_in_room, '$room_class', '$time_from', '$time_to') ";
        $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
        $row = mysqli_fetch_row($result)[0];

        $query2 =
            "
select count(distinct room_id)
from hotel.rooms 
where  building_id = $hotel and 
               $pers_in_room = capacity and
               '$room_class' = room_class  
               
               ";
        $result2 = mysqli_query($link, $query2) or die("Ошибка " . mysqli_error($link));
        $row2 = mysqli_fetch_row($result2)[0];
        echo "free  rooms: $row from $row2";

    }


}


mysqli_close($link);

?>

<p><a href="staff.php">GO TO STAFF PAGE</a></p>
</body>
</html>