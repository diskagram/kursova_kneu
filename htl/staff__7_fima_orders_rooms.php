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
$query = "SELECT partner_id, partner_name  FROM hotel.partners";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='partner'>hotel: </label> 
   <select name='partner' >";
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
         <label for='show_all_time'>show all time data: </label>
         <input type='checkbox' name='show_all_time' value='Yes' />
         
      </p>  
        <p> <input type='submit' value='reveal data'> </p>  
        
         
</form>
 ";


if (isset($_POST['partner'])
    && isset($_POST['time_from'])
    && isset($_POST['time_to'])
) {
    $partner = htmlentities(mysqli_real_escape_string($link, $_POST['partner']));
    $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
    $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());
    $show_all_time = htmlentities(mysqli_real_escape_string($link, $_POST['show_all_time']));

    echo "

<div id='piechart'></div>
<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
<script type='text/javascript'>
// Load google charts
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

// Draw the chart and set the chart values
function drawChart() {
  var data = google.visualization.arrayToDataTable([";

if ($show_all_time == 'Yes') {
    $query = "select concat(room_class, ' ', capacity, 'pers'), count(order_id)
from hotel.room_orders 
    left join hotel.partners p on p.partner_id = room_orders.partner_id
    left join hotel.rooms r on room_orders.room_id = r.room_id
where p.partner_id = $partner
group by 1
";}
else {

    $query = "select concat(room_class, ' ', capacity, 'pers'), count(order_id)
from hotel.room_orders 
    left join hotel.partners p on p.partner_id = room_orders.partner_id
    left join hotel.rooms r on room_orders.room_id = r.room_id
where p.partner_id = $partner and date(create_timestamp) between    date('$time_from') and  date('$time_to')
group by 1 ";}


    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    echo "['room_type', 'orders'],";
    for ($i = 0; $i < $table_data_rows_number; ++$i) {
        $rows = mysqli_fetch_row($result);
        echo "['$rows[0]',$rows[1]],";
    }


    echo "]);

  // Optional; add a title and set the width and height of the chart
  var options = {'title':'Room rent by partner', 'width':550, 'height':400};

  // Display the chart inside the <div> element with id='piechart'
  var chart = new google.visualization.PieChart(document.getElementById('piechart'));
  chart.draw(data, options);
}
</script>";


}


mysqli_close($link);
?>
<p><a href="staff.php">GO TO STAFF PAGE</a></p>
</body>
</html>