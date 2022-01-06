<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{er_title}</h1>
        <a href="admin.php?page=errors&deleteall=yes" class="btn btn-danger btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-trash-alt"></i>
            </span>
            <span class="text">{er_delete_all}</span>
        </a>
    </div>
    <p class="mb-4">{er_sub_title}</p>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseErrors" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseErrors">
                    <h6 class="m-0 font-weight-bold text-primary">{er_error_list}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseErrors" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{er_user_ip}</th>
                                        <th>{er_type}</th>
                                        <th>{er_code}</th>
                                        <th>{er_data}</th>
                                        <th>{er_track}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {errors_list}
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-{alert_type}" role="alert">
                                                {error_message}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{user_ip}</td>
                                        <td>{error_type}</td>
                                        <td>{error_code}</td>
                                        <td>{error_datetime}</td>
                                        <td>{error_trace}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            <hr>
                                        </td>
                                    </tr>
                                    {/errors_list}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5">{errors_list_resume}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"></h1>
        <a href="admin.php?page=errors&deleteall=yes" class="btn btn-danger btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-trash-alt"></i>
            </span>
            <span class="text">{er_delete_all}</span>
        </a>
    </div>
</div>