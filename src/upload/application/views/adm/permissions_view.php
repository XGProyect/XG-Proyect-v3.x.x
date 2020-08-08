
<div class="container-fluid">
    {alert}
    <form action="" method="POST" name="changelog">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{pr_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{pr_save_all}</span>
            </button>
        </div>
        <p class="mb-4">{pr_sub_title}</p>

        {sections_list}
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapse{section_name}" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapse{section_name}">
                        <h6 class="m-0 font-weight-bold text-primary">{section_title}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapse{section_name}" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="25%"></th>
                                            {roles_list}
                                            <th width="25%" class="text-center">{role_name}</th>
                                            {/roles_list}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {modules_list}
                                        <tr>
                                            <td>
                                                <a href="admin.php?page={page_module}">{page_module_title}</a>
                                            </td>
                                            {permissions_list}
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" name="{module}[{role}]" {permission_checked} {permission_disabled}>
                                            </td>
                                            {/permissions_list}
                                        </tr>
                                        {/modules_list}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/sections_list}
    </form>
</div>
