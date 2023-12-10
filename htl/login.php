<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<?php

echo "
<form method='POST'>
    <p><b>log in page</b></p>
    <p><input placeholder='login' type='text' name='login'/></p>
    <p><input placeholder='password' type='text' name='password'/></p>
    <input type='submit' value='sign in'>
</form>
";

session_start(); // to correctly check $_SESSION variable
$is_user_auth = $_SESSION['loggedin'];
$user_role = $_SESSION['user_role'];

if ($is_user_auth == 1) { // if logged in user is trying to visit login page
    switch ($user_role) {
        case 'partner':
            header("Location: partner.php");
            break;
        case 'staff':
            header("Location: staff.php");
            break;
        case 'single':
            header("Location: customer.php");
            break;

    }
}

require_once 'connection.php';
$link = mysqli_connect($db_host, $db_user, $db_password, $db_db)
or die("Помилка " . mysqli_error($link));

$user_role = mysqli_real_escape_string($link, $_GET['role']);

if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = htmlentities(mysqli_real_escape_string($link, $_POST['login']));
    $password = htmlentities(mysqli_real_escape_string($link, $_POST['password']));

    if (strlen($password) > 4) { //check password length+ fix bug with accept empty creds
        $query = "SELECT password FROM hotel.site_passwords where login = '$login' and user_role = '$user_role' ";
        $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));


        if ($result) {
            $password_get = mysqli_fetch_row($result)[0];
            if ($password == $password_get) {
                $_SESSION['loggedin'] = 1;
                switch ($user_role) {
                    case 'partner':
                        $_SESSION['user_role'] = 'partner';
                        header("Location: partner.php");
                        break;
                    case 'staff':
                        $_SESSION['user_role'] = 'staff';
                        header("Location: staff.php");
                        break;
                    case 'single':

                        $_SESSION['user_role'] = 'single';
                        header("Location: customer.php");
                        break;

                }
            } else echo "<p style='color:red;'>bad credentials, try again</p>";
        }
    } else {
        echo "<p style='color:red;'>password should be > 4 </p>";
    }
}

if ($user_role == 'single') {
    echo "
            <form method='POST'>
            <p><b>Not a user? Create an account</b></p>
            <p><input placeholder='login' type='text' name='login_create'/></p>
            <p><input placeholder='password' type='text' name='password_create'/></p>
            <input type='submit' value='sign up'>
            </form>
            ";

    if (isset($_POST['login_create']) && isset($_POST['password_create'])) {

        $login_create = htmlentities(mysqli_real_escape_string($link, $_POST['login_create']));
        $password_create = htmlentities(mysqli_real_escape_string($link, $_POST['password_create']));

        //check if login already exists

        if (strlen($password_create) > 4) {
            $query = "SELECT * FROM hotel.site_passwords where login = '$login_create' ";
            $result2 = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
            $table_data_rows_number = mysqli_num_rows($result2); // number of rows in response


            if ($result2 and $table_data_rows_number == 0) {
                $query = "insert into hotel.site_passwords  (login, password, user_role) 
                        values( '$login_create', '$password_create', 'single') ";
                $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
                $_SESSION['loggedin'] = 1;
                header("Location: customer.php");
            } else echo "<p style='color:red;'>user is already exist</p>";
        } else {
            echo "<p style='color:red;'>password should be > 4 </p>";
        }
    }
}

?>
<p><a href="index.php">GO TO MAIN PAGE</a></p>
</body>
</html>
