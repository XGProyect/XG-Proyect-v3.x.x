
<div class="container-fluid">
    {alert}
    <form action="" method="POST" name="changelog">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{pr_title}</h1>
            <a href="admin.php?page=changelog&action=add" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{pr_save_all}</span>
            </a>
        </div>
        <p class="mb-4">{pr_sub_title}</p>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{pr_general}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{ge_go}</th>
                                            <th>{ge_sgo}</th>
                                            <th>{ge_ga}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {permissions_list}
                                        <tr>
                                            <td><a href="admin.php?page={page_module}">{page_module}</a></td>
                                            <td><input class="form-check-input" type="checkbox" name="{page_module}[{go_role}]" {go_checked}></td>
                                            <td><input class="form-check-input" type="checkbox" name="{page_module}[{sgo_role}]" {sgo_checked}></td>
                                            <td><input class="form-check-input" type="checkbox" name="{page_module}[{ga_role}]" {ga_checked} disabled></td>
                                        </tr>
                                        {/permissions_list}
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
