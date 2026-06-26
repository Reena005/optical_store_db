<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: ../admin/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Clarity Optical Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body{
            margin:0;
            min-height:100vh;
            background:linear-gradient(135deg,#0f172a 0%,#1d4ed8 55%,#60a5fa 100%);
            overflow:hidden;
            font-family:'Segoe UI',sans-serif;
            position:relative;
        }

        body::before{
            content:'';
            position:absolute;
            width:450px;
            height:450px;
            background:rgba(255,255,255,.08);
            border-radius:50%;
            top:-120px;
            left:-120px;
        }

        body::after{
            content:'';
            position:absolute;
            width:500px;
            height:500px;
            background:rgba(255,255,255,.06);
            border-radius:50%;
            right:-180px;
            bottom:-180px;
        }

        .login-wrapper{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
            z-index:2;
        }

        .login-card{
            width:520px;
            border:none;
            border-radius:30px;
            overflow:hidden;
            background:white;
            box-shadow:0 30px 80px rgba(0,0,0,.30);
            animation:fadeUp .7s ease;
        }

        .login-header{
            background:linear-gradient(135deg,#2563eb,#1e3a8a);
            padding:45px 30px;
            text-align:center;
            color:white;
        }

        .logo{
            width:90px;
            height:90px;
            margin:0 auto 20px;
            border-radius:50%;
            background:rgba(255,255,255,.15);
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:42px;
            border:2px solid rgba(255,255,255,.25);
        }

        .login-header h2{
            font-weight:800;
            font-size:36px;
            margin-bottom:5px;
        }

        .login-header p{
            opacity:.9;
            font-size:17px;
            margin-bottom:0;
        }

        .card-body{
            padding:35px;
        }

        label{
            font-weight:600;
            margin-bottom:8px;
            color:#1f2937;
        }

        .form-control{
            height:55px;
            border-radius:14px;
            border:2px solid #e2e8f0;
            padding-left:18px;
            font-size:16px;
        }

        .form-control:focus{
            border-color:#2563eb;
            box-shadow:0 0 15px rgba(37,99,235,.20);
        }

        .btn-login{
            height:58px;
            font-size:19px;
            font-weight:700;
            border-radius:14px;
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            border:none;
        }

        .btn-login:hover{
            box-shadow:0 12px 25px rgba(37,99,235,.35);
        }

        .footer-text{
            margin-top:25px;
            text-align:center;
            color:#94a3b8;
            font-size:15px;
        }

        .alert{
            border-radius:14px;
        }

        @keyframes fadeUp{
            from{
                opacity:0;
                transform:translateY(50px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-header">
            <div class="logo">
                <i class="bi bi-eyeglasses"></i>
            </div>

            <h2>Clarity Optical</h2>
            <p>Secure Administrator Portal</p>
        </div>

        <div class="card-body">

            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php } ?>

            <form action="login_process.php" method="POST">

                <div class="mb-3">
                    <label>Email Address</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter admin email"
                        required>
                </div>

                <div class="mb-4">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter password"
                        required>
                </div>

                <button class="btn btn-primary btn-login w-100">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Login
                </button>

            </form>

            <div class="footer-text">
                Optical Store Management System © 2026
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>