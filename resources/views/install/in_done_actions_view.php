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
                        <input type="hidden" name="page" value="{step}" />
                        <div class="text-center">
                            
                            <h4>
                                {done_config}
                                {done_connected}
                                {done_insert}
                            </h4>
                            <input type="button" class="btn btn-primary" name="next" onclick="submit();"
                                value="{ins_continue}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
