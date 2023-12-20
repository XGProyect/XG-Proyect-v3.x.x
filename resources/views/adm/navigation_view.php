<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark shadow">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="admin.php?page=home">
        <img src="https://xgproyect.org/wp-content/uploads/2019/10/xgp-new-logo-white.png" alt="XG Proyect Logo"
            title="XG Proyect" width="150px">
    </a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!" title="Toogle Sidebar">
        <i class="fa fa-bars fs-4"></i>
    </button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge bg-danger badge-counter"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center"
                        href="https://github.com/XGProyect/XG-Proyect-v3.x.x/releases" target="_blank">
                        <div class="me-2">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-download text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{current_date}</div>
                            <span class="fw-bold">{nv_new_download}</span>
                        </div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-user fa-fw"></i>
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{user_name}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        {nv_logout}
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
