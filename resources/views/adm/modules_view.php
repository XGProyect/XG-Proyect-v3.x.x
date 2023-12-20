<div class="container-fluid px-4 pt-3">
    {alert}
    <form name="frm_modules" method="POST" action="admin.php?page=modules">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{mdl_title}</h1>
            <button type="submit" name="save" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{mdl_save}</span>
            </button>
        </div>
        <p class="mb-4">{mdl_sub_title}</p>

        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    {modules}
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-3">
                        <div class="card border-left-{color} shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-{color} text-uppercase mb-1">
                                            {module_name}</div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="status"
                                                    name="status{module}" {module_value}>
                                                <label class="font-weight-bold text-primary text-uppercase mb-1"
                                                    style="font-size:.75rem" for="status{module}">
                                                    Cambiar estado
                                                </label>
                                            </div>
                                            <!-- <input type="checkbox" name="status{module}" id="status" {module_value}> -->
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/modules}
                </div>
            </div>
        </div>
    </form>
</div>