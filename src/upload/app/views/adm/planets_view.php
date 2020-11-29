<div class="container-fluid">
    {alert}
    <form action="" method="POST">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{np_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{np_save_parameters}</span>
            </button>
        </div>
        <p class="mb-4">{np_sub_title}</p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{np_general}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span>
                                                    {np_initial_fields}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="initial_fields"
                                                    maxlength="10" value="{initial_fields}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseOtherParameters" class="d-block card-header py-3" data-toggle="collapse"
                        role="button" aria-expanded="true" aria-controls="collapseOtherParameters">
                        <h6 class="m-0 font-weight-bold text-primary">{np_production}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseOtherParameters" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span>
                                                    {np_metal_production}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="metal_basic_income"
                                                    maxlength="10" value="{metal_basic_income}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {np_crystal_production}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="crystal_basic_income"
                                                    maxlength="10" value="{crystal_basic_income}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {np_deuterium_production}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="deuterium_basic_income"
                                                    maxlength="10" value="{deuterium_basic_income}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {np_energy_production}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="energy_basic_income"
                                                    maxlength="10" value="{energy_basic_income}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
