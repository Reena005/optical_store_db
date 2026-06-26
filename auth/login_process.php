<?php

session_start();

require("../config/database.php");

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE email = $1";

    $result = pg_query_params($conn, $query, array($email));

    if(pg_num_rows($result)==1)
    {
        $user = pg_fetch_assoc($result);

        if(password_verify($password, $user['password']))
        {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../admin/dashboard.php");
            exit();
        }
        else
        {
            $_SESSION['error']="Invalid Password";
            header("Location: login.php");
            exit();
        }
    }
    else
    {
        $_SESSION['error']="Invalid Email";
        header("Location: login.php");
        exit();
    }
}
?>