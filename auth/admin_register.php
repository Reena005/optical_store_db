<?php
require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password != $confirm) {
        $error = "Passwords do not match.";
    } else {

        $check = pg_query_params(
            $conn,
            "SELECT * FROM users WHERE email=$1",
            array($email)
        );

        if (pg_num_rows($check) > 0) {
            $error = "Email already exists.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insert = pg_query_params(
                $conn,
                "INSERT INTO users(full_name,email,password,role)
                 VALUES($1,$2,$3,'Admin')",
                array($name,$email,$hashedPassword)
            );

            if($insert){
                $_SESSION['success']="Administrator account created successfully.";
                header("Location: login.php");
                exit();
            }else{
                $error="Unable to create administrator.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Registration | Clarity Optical Store</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
*{
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    margin:0;
    min-height:100vh;
    background:
        radial-gradient(circle at top left, rgba(96,165,250,.35), transparent 35%),
        radial-gradient(circle at bottom right, rgba(37,99,235,.35), transparent 35%),
        linear-gradient(135deg,#020617,#0f172a,#1d4ed8);
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}

.register-wrapper{
    width:100%;
    max-width:1100px;
    min-height:650px;
    display:grid;
    grid-template-columns:1fr 1fr;
    background:rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.18);
    border-radius:32px;
    overflow:hidden;
    box-shadow:0 30px 90px rgba(0,0,0,.40);
    backdrop-filter:blur(18px);
}

.register-left{
    padding:55px;
    color:white;
    background:linear-gradient(135deg,rgba(37,99,235,.85),rgba(15,23,42,.95));
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.logo-circle{
    width:90px;
    height:90px;
    border-radius:50%;
    background:rgba(255,255,255,.15);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:42px;
    margin-bottom:25px;
    border:1px solid rgba(255,255,255,.25);
}

.register-left h1{
    font-size:44px;
    font-weight:800;
    margin-bottom:15px;
}

.register-left p{
    color:#dbeafe;
    font-size:17px;
    line-height:1.7;
}

.feature{
    display:flex;
    align-items:center;
    gap:14px;
    margin-top:18px;
    color:#eef2ff;
}

.feature i{
    font-size:22px;
    color:#93c5fd;
}

.register-right{
    background:white;
    padding:50px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.register-right h2{
    font-weight:800;
    color:#0f172a;
}

.register-right .subtitle{
    color:#64748b;
    margin-bottom:30px;
}

.form-label{
    font-weight:700;
    color:#1f2937;
}

.form-control{
    height:54px;
    border-radius:14px;
    border:2px solid #e5e7eb;
    padding-left:16px;
}

.form-control:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.12);
}

.btn-register{
    height:56px;
    border-radius:14px;
    border:none;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:white;
    font-weight:800;
    font-size:17px;
    transition:.3s;
}

.btn-register:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 30px rgba(37,99,235,.35);
    color:white;
}

.alert{
    border-radius:14px;
}

.login-link{
    text-align:center;
    margin-top:25px;
    color:#64748b;
}

.login-link a{
    font-weight:800;
    color:#2563eb;
    text-decoration:none;
}

.login-link a:hover{
    text-decoration:underline;
}

@media(max-width:900px){
    .register-wrapper{
        grid-template-columns:1fr;
        margin:20px;
    }

    .register-left{
        display:none;
    }
}
</style>
</head>

<body>

<div class="register-wrapper">

    <div class="register-left">

        <div class="logo-circle">
            <i class="bi bi-eyeglasses"></i>
        </div>

        <h1>Clarity Optical Store</h1>

        <p>
            Create a secure administrator account to manage customers,
            inventory, suppliers, billing, invoices and business reports.
        </p>

        <div class="feature">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Secure admin access with hashed passwords</span>
        </div>

        <div class="feature">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Manage optical billing and invoices</span>
        </div>

        <div class="feature">
            <i class="bi bi-box-seam-fill"></i>
            <span>Track stock and low inventory alerts</span>
        </div>

    </div>

    <div class="register-right">

        <h2>Admin Registration</h2>
        <p class="subtitle">Create your administrator account</p>

        <?php if(isset($error)){ ?>
            <div class="alert alert-danger">
                <?= $error; ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter admin email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create password" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
            </div>

            <button class="btn btn-register w-100">
                <i class="bi bi-person-plus-fill"></i>
                Create Administrator
            </button>

        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login here</a>
        </div>

    </div>

</div>

</body>
</html>