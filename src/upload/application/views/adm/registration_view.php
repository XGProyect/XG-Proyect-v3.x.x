<div class="container-fluid">
    {alert}
    <form action="" method="POST">
        <input type="hidden" name="save" value="1">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{ur_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{ur_save_parameters}</span>
            </button>
        </div>
        <p class="mb-4">{ur_sub_title}</p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{ur_general}</h6>
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
                                                    {ur_open_close}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-check" type="checkbox" name="reg_enable"
                                                    {reg_enable}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ur_welcome_message}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-check" type="checkbox" name="reg_welcome_message"
                                                    {reg_welcome_message}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ur_welcome_email}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-check" type="checkbox" name="reg_welcome_email"
                                                    {reg_welcome_email}>
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
