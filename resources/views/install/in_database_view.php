<div class="container mt-4">
    <div class="row">
        {alert}
        <div class="col-xl-9 col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{ins_install_title}</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="page" value="step1" />
                        <h4 class="text-center">{ins_connection_data_title}</h4>

                        <div class="mb-3">
                            <label for="ins_server_title" class="form-label">{ins_server_title}</label>
                            <input type="text" class="form-control" id="host" name="host"
                                placeholder="{ins_ex_tag} localhost" value="{v_host}">
                        </div>

                        <div class="mb-3">
                            <label for="ins_user_title" class="form-label">{ins_user_title}</label>
                            <input type="text" class="form-control" id="user" name="user"
                                placeholder="{ins_ex_tag} root" value="{v_user}">
                        </div>

                        <div class="mb-3">
                            <label for="ins_password_title" class="form-label">{ins_password_title}</label>
                            <input type="password" class="form-control" id="password" name="password"
                                autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="ins_db_title" class="form-label">{ins_db_title}</label>
                            <input type="text" class="form-control" id="db" name="db"
                                placeholder="{ins_ex_tag} xgproyect" value="{v_db}">
                        </div>

                        <div class="mb-3">
                            <label for="ins_prefix_title" class="form-label">{ins_prefix_title}</label>
                            <input type="text" class="form-control" id="prefix" name="prefix"
                                placeholder="{ins_ex_tag} xgp_" value="{v_prefix}">
                        </div>

                        <div class="text-center">
                            <input type="button" class="btn btn-primary" onclick="submit();" value="{ins_install_go}">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
