<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card border-0 shadow my-5">
                <div class="card-body p-0 bg-login-image rounded">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block my-auto text-center">
                            <img src="https://xgproyect.org/wp-content/uploads/2019/10/xgp-new-logo-white.png"
                                alt="XG Proyect Logo" title="XG Proyect" class="img-fluid" style="width: 250px;">
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-white mb-4">{lg_welcome_back}</h1>
                                </div>
                                <form class="user" method="post" action="admin.php?page=login&redirect={redirect}">
                                    <div class="form-group mb-3">
                                        <input type="email" class="form-control form-control-user" id="inputEmail"
                                            name="inputEmail" aria-describedby="emailHelp"
                                            placeholder="{lg_enter_email_address}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="password" class="form-control form-control-user" id="inputPassword"
                                            name="inputPassword" placeholder="{lg_password}">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block"
                                        name="signin">{lg_login}</button>
                                </form>
                                <br>
                                {alert}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>