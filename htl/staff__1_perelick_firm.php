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

echo "<h>1.	Отримати перелік і загальне число фірм, що забронювали місця в обсязі, не менше вказаного, за весь період співпраці, або за деякий період. </h>";
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
      
      <p> 
       
       <label for='time_to'>min_persons: </label>
         <input type='text' name='min_persons'>
         
           <label for='show_all_time'>show all time data: </label>
         <input type='checkbox' name='show_all_time' value='Yes' />
         
      </p> <input type='submit' value='find data about partner'> </p>  
          
          
          
          
</form>
 ";


if (isset($_POST['min_persons'])
    && isset($_POST['time_from'])
    && isset($_POST['time_to'])
) {
    $min_persons = htmlentities(mysqli_real_escape_string($link, $_POST['min_persons'])) ?: 0;
    $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
    $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());
    $show_all_time = htmlentities(mysqli_real_escape_string($link, $_POST['show_all_time']));


////////output all
    if ($show_all_time != 'Yes') {
        $query =
            "
select   partners.partner_id, partner_name, discount_level, count(order_id)
from hotel.room_orders
inner join hotel.partners on partners.partner_id = room_orders.partner_id
where  date(create_timestamp) between    date('$time_from') and  date('$time_to')
group by 1,2,3
having count(order_id)>$min_persons
               ";

        $query2 =
            "
select count(distinct partner_id) from (
select   partners.partner_id, partner_name, discount_level, count(order_id)
from hotel.room_orders
inner join hotel.partners on partners.partner_id = room_orders.partner_id
where  date(create_timestamp) between   date ('$time_from') and  date('$time_to')
group by 1,2,3
having count(order_id)>$min_persons) bs
            
            
               ";


    } else {
        $query =
            "select distinct partners.partner_id, partner_name, discount_level, count(order_id)
    from hotel.room_orders
       inner join hotel.partners on partners.partner_id = room_orders.partner_id
group by 1,2,3
having count(order_id)>$min_persons

               ";
        $query2 =
            "
select count(distinct partner_id) from (
            select   partners.partner_id, partner_name, discount_level, count(order_id)
    from hotel.room_orders
       inner join hotel.partners on partners.partner_id = room_orders.partner_id
group by 1,2,3
having count(order_id)>$min_persons) bs
               ";
    }

    $result2 = mysqli_query($link, $query2) or die("Ошибка " . mysqli_error($link));
    $row2 = mysqli_fetch_row($result2)[0];
    echo "<p>amount af partners: $row2</p>";


    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<thead> <tr>";
    echo "<th>id</th><th>name</th><th>discount_level</th><th>persons</th>";
    echo "</tr></thead>";
    echo "<tbody>";
    for ($i = 0; $i < $table_data_rows_number; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";
        for ($j = 0; $j < $table_data_columns_number; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";


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
        $query = "select ifnull(partner_name,'Not a partner'), count(room_id)
from hotel.room_orders left join hotel.partners p on p.partner_id = room_orders.partner_id
group by 1
having count(room_id)>$min_persons;
";
    } else {

        $query = "select ifnull(partner_name,'Not a partner'), count(room_id)
from hotel.room_orders left join hotel.partners p on p.partner_id = room_orders.partner_id
where  date(create_timestamp) between   '$time_from' and  '$time_to'
group by 1
having count(room_id)>$min_persons;
";
    }
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    echo "['partner', 'orders'],";
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

?>

<p><a href="staff.php">GO TO STAFF PAGE</a></p>
</body>
</html>