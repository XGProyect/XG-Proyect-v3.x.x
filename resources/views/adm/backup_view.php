<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{bku_title}</h1>
        <form name="frm_backup_now" method="POST" action="admin.php?page=backup">
            <input type="hidden" name="backup" value="1">
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-download"></i>
                </span>
                <span class="text">{bku_now}</span>
            </button>
        </form>
    </div>
    <p class="mb-4">{bku_sub_title}</p>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseGeneral">
                    <h6 class="m-0 font-weight-bold text-primary">{bku_general}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseGeneral" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form name="frm_backup" method="POST" action="admin.php?page=backup">
                                <input type="hidden" name="save" value="1">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span>
                                                    {bku_auto}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{bku_auto_legend}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-check-input" type="checkbox" name="auto_backup"
                                                    {auto_backup}>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{bku_save}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseList" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseList">
                    <h6 class="m-0 font-weight-bold text-primary">{bku_list}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseList" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless" width="100%" cellspacing="0">
                                {backup_list}
                                <tr>
                                    <td>
                                        {file_name}
                                    </td>
                                    <td>
                                        {file_size}
                                    </td>
                                    <td>
                                        <a href="admin.php?page=backup&action=download&file={full_file_name}"
                                            target="_blank" class="btn btn-primary btn-circle btn-sm">
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                        <a href="admin.php?page=backup&action=delete&file={full_file_name}"
                                            class="btn btn-danger btn-circle btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                {/backup_list}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
