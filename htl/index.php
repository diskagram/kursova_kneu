<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styles.css">
    <h> Hotel Networks</h>
</head>
<body>
<?php
session_start();
$_SESSION['loggedin'] = 0;
$_SESSION['user_role'] = 'nobody';
$is_user_auth = $_SESSION['loggedin'];
$user_role = $_SESSION['user_role'];
?>
<p><a href="login.php?role=single">I am a single customer</a></p>
<p><a href="login.php?role=partner">I am a partner</a></p>
<p><a href="login.php?role=staff">I am stuff</a></p>
<p>What do people talk about us?</p>
<?php
require_once 'connection.php';
$link = mysqli_connect($db_host, $db_user, $db_password, $db_db)
or die("Помилка " . mysqli_error($link));

echo "<form method = 'post'>";

$query = "SELECT building_id, building_name  FROM hotel.buildings";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);
echo " <label for='hotel'>hotel: </label> 
   <select name='hotel' >";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[1]</option>";
}
echo "  </select> ";
echo "<select name='stars'>
             <option value=all>all</option>
             <option value=1>1</option>
             <option value=2>2</option>
             <option value=3>3</option>
             <option value=4>4</option>
             <option value=5>5</option>
         </select>
         <input type='submit' value='view reviews'> 
         ";
echo "</form>";


if (isset($_POST['hotel'])
    && isset($_POST['stars'])) {
    $hotel = htmlentities(mysqli_real_escape_string($link, $_POST['hotel']));
    $stars = htmlentities(mysqli_real_escape_string($link, $_POST['stars']));

    if ($stars != 'all') {
        $query = "SELECT response_text , response_stars, full_mane
          FROM hotel.customer_reviews 
          left join hotel.customers c on c.passport_id = customer_reviews.customer_passport_id
          where building_id  = '$hotel' and
                response_stars = $stars;";
    } else {
        $query = "SELECT response_text , response_stars, full_mane
          FROM hotel.customer_reviews 
          left join hotel.customers c on c.passport_id = customer_reviews.customer_passport_id
          where building_id  = '$hotel' 
          order by 2 desc";
    }

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    $table_data_columns_number = mysqli_num_fields($result);

    echo "<table>";
    echo "<thead> <tr>";
    echo "<th>response</th><th>stars</th><th>reviewer name</th>";
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

?>
</body>
</html>