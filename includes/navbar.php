<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-eyeglasses"></i>
            Optical Store
        </a>

        <div class="d-flex align-items-center">

            <span class="text-white me-3">

                Welcome,

                <strong>

                    <?php echo $_SESSION['name']; ?>

                </strong>

            </span>

            <a href="../auth/logout.php" class="btn btn-light btn-sm">

                <i class="bi bi-box-arrow-right"></i>

                Logout

            </a>

        </div>

    </div>

</nav>