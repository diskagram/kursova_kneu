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
if ($is_user_auth == 0 or $user_role != 'partner') header("Location: index.php");
require_once 'connection.php';
$link = mysqli_connect($db_host, $db_user, $db_password, $db_db)
or die("Помилка " . mysqli_error($link));
////////////
$query = "SELECT TABLE_NAME FROM information_schema.TABLES
where TABLE_SCHEMA = 'hotel'
";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='table'>table: </label> 
   <select name='table' >";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[0]</option>";
}
echo " 
   </select> 
      
         
 
        <p> <input type='submit' value='reveal data'> </p>  
        
         
</form>
 ";

if (isset($_POST['table'])

) {
$table = htmlentities(mysqli_real_escape_string($link, $_POST['table']));

$query = "SELECT * FROM $table";
$query_columns = "
            SELECT    
                COLUMN_NAME
            FROM 
            information_schema.COLUMNS
            WHERE
            TABLE_SCHEMA = '$db_db' and TABLE_NAME  = '$table'
            order by ORDINAL_POSITION
            ";


$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$columns_labels = mysqli_query($link, $query_columns) or die("Ошибка " . mysqli_error($link));

if ($result && $columns_labels) {
    $table_data_rows_number = mysqli_num_rows($result); // количество полученных строк данных
    $table_data_columns_number = mysqli_num_fields($result);// количество полученных колонок

    $table_header_rows_number = mysqli_num_rows($columns_labels); // количество полученных строк данных

    echo "<table>";
    echo "<caption>output table '$table'</caption>";


    echo "<thead><tr>";

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

}}

mysqli_close($link);

?>


</body>
</html>