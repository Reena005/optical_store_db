<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Clarity Optical Store</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{

height:100vh;

background:
linear-gradient(115deg,rgba(15,23,42,.90),rgba(30,64,175,.85)),
url("assets/images/optical.jpg");

background-size:cover;
background-position:center;

overflow:hidden;

}

.hero{

height:100vh;

display:flex;

align-items:center;

justify-content:space-between;

padding:0 8%;

color:white;

}

.left{

width:55%;

}

.left h5{

letter-spacing:5px;

text-transform:uppercase;

color:#93c5fd;

margin-bottom:20px;

}

.left h1{

font-size:70px;

font-weight:800;

line-height:1.1;

margin-bottom:20px;

}

.left span{

color:#60a5fa;

}

.left p{

font-size:19px;

color:#d1d5db;

width:85%;

margin-bottom:40px;

}

.buttons{

display:flex;

gap:20px;

}

.btn-login{

background:#2563eb;

color:white;

padding:16px 40px;

border-radius:50px;

text-decoration:none;

font-weight:700;

transition:.3s;

}

.btn-login:hover{

background:#1d4ed8;

transform:translateY(-5px);

color:white;

}

.btn-signup{

border:2px solid white;

padding:16px 40px;

border-radius:50px;

text-decoration:none;

font-weight:700;

color:white;

transition:.3s;

}

.btn-signup:hover{

background:white;

color:#111827;

transform:translateY(-5px);

}

.right{

width:40%;

display:flex;

justify-content:center;

}

.glass{

width:420px;

padding:45px;

border-radius:30px;

background:rgba(255,255,255,.08);

backdrop-filter:blur(18px);

border:1px solid rgba(255,255,255,.15);

box-shadow:0 20px 60px rgba(0,0,0,.35);

}

.glass h3{

margin-bottom:25px;

font-weight:700;

}

.info{

display:flex;

align-items:center;

margin:20px 0;

}

.info i{

font-size:28px;

margin-right:18px;

color:#60a5fa;

}

.info h6{

margin:0;

font-weight:600;

}

.info small{

color:#d1d5db;

}

footer{

position:absolute;

bottom:20px;

left:8%;

color:#cbd5e1;

}

</style>

</head>

<body>

<div class="hero">

<div class="left">

<h5>WELCOME TO</h5>

<h1>

Clarity <span>Optical</span><br>

Store

</h1>

<p>

A modern Optical Store Management System to manage customers, products,
suppliers, billing, inventory, reports and invoices with complete security
and efficiency.

</p>

<div class="buttons">

<a href="auth/login.php" class="btn-login">

<i class="bi bi-box-arrow-in-right"></i>

Admin Login

</a>

<a href="auth/admin_register.php" class="btn-signup">

<i class="bi bi-person-plus-fill"></i>

Admin Registration

</a>

</div>

</div>

<div class="right">

<div class="glass">

<h3>

<i class="bi bi-eyeglasses"></i>

Why Clarity?

</h3>

<div class="info">

<i class="bi bi-person-lines-fill"></i>

<div>

<h6>Customer Management</h6>

<small>Maintain customer records easily</small>

</div>

</div>

<div class="info">

<i class="bi bi-box-seam"></i>

<div>

<h6>Inventory Control</h6>

<small>Real-time product & stock management</small>

</div>

</div>

<div class="info">

<i class="bi bi-receipt-cutoff"></i>

<div>

<h6>Billing System</h6>

<small>Generate invoices instantly</small>

</div>

</div>

<div class="info">

<i class="bi bi-bar-chart-line-fill"></i>

<div>

<h6>Business Reports</h6>

<small>Sales, stock & customer analytics</small>

</div>

</div>

</div>

</div>

</div>

<footer>

© 2026 Clarity Optical Store | Secure Admin Portal

</footer>

</body>

</html>