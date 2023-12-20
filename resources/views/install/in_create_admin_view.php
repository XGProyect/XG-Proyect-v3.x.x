<div class="container mt-4">
    <div class="row">
        <div class="col-xl-9 col-lg-8 mx-auto">
            {alert}
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{ins_install_title}</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="page" value="{step}" />
                        <h4 class="text-center">{ins_admin_create_title}</h4>

                        <div class="mb-3">
                            <label for="adm_user" class="form-label">{ins_admin_create_user}</label>
                            <input name="adm_user" id="adm_user" class="form-control" size="20" maxlength="20"
                                type="text">
                        </div>

                        <div class="mb-3">
                            <label for="adm_pass" class="form-label">{ins_admin_create_pass}</label>
                            <input name="adm_pass" id="adm_pass" class="form-control" size="20" maxlength="20"
                                type="password" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="adm_email" class="form-label">{ins_admin_create_email}</label>
                            <input name="adm_email" id="adm_email" class="form-control" size="20" maxlength="40"
                                type="email">
                        </div>

                        <div class="text-center">
                            <input type="button" class="btn btn-primary" onclick="submit();"
                                value="{ins_admin_create_create}">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>