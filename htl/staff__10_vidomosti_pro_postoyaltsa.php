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

echo "<h>10.	Отримати відомості про постояльця із заданого номера: його рахунок готелю за додаткові послуги, які надходили від нього скарги, види додаткових послуг, якими він користувався</h>";

$query = "SELECT passport_id, full_mane  FROM hotel.customers";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='customer'>customer: </label> 
   <select name='customer' >";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[1]</option>";
}
echo " 
        <input type='submit' value='show customer info'>
</form>
 ";


if (isset($_POST['customer'])
) {
    $customer = htmlentities(mysqli_real_escape_string($link, $_POST['customer']));

    $query =
        "
       select customer_full_name,
       customer_email,
       customer_phone,
       room_orders.room_id,
       order_start_time,
       order_end_time,
       partner_id,
       order_amount_usd,
       capacity as room_capacity,
       room_class,
       storey
from hotel.room_orders
         left join hotel.rooms r on room_orders.room_id = r.room_id
where customer_passport_id = '$customer'
        ";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<caption>all room orders by customer</caption>";
    echo "<thead> <tr>";
    echo " <th>customer_full_name</th>
           <th>customer_email</th>
           <th>customer_phone</th>
           <th>room_id</th>
           <th>order_start_time</th>
           <th>order_end_time</th>
           <th>partner_id</th>
           <th>order_amount_usd</th>
           <th>room_capacity</th>
           <th>room_class</th>
           <th>storey</th>";
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

if (isset($_POST['customer'])
) {
    $customer = htmlentities(mysqli_real_escape_string($link, $_POST['customer']));

    $query =
        "
select full_mane,
       building_name,
       response_text,
       response_stars
from hotel.customer_reviews
         left join hotel.customers on customer_passport_id = passport_id
         left join hotel.buildings b on b.building_id = customer_reviews.building_id
where customer_passport_id = '$customer'
        ";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<caption>all reviews by customer</caption>";
    echo "<thead> <tr>";
    echo " <th>customer_full_name</th>
           <th>hotel</th>
           <th>response text</th>
           <th>response stars</th>
                             ";
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

if (isset($_POST['customer'])
) {
    $customer = htmlentities(mysqli_real_escape_string($link, $_POST['customer']));

    $query =
        "
        select full_mane,
       order_date,
       service_title,
       service_orders.price_usd

from hotel.service_orders
         left join hotel.services s on s.service_id = service_orders.service_id
         left join hotel.customers on customer_passport_id = passport_id
where customer_passport_id = '$customer'
          ";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<caption>all additional services by customer</caption>";
    echo "<thead> <tr>";
    echo " <th>customer_full_name</th>
           <th>order date</th>
           <th>service title</th>
           <th>price per service</th>
                             ";
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