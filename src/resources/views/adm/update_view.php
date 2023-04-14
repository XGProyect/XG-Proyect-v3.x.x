<div class="container-fluid">
    {alert}
    <form name="update_form" method="post" action="">
        <input type="hidden" name="send" value="send">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{up_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-sync-alt"></i>
                </span>
                <span class="text">{up_go}</span>
            </button>
        </div>
        <p class="mb-4">{up_sub_title}</p>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{up_test_mode}</h6>
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
                                                    <input class="form-check-input" type="checkbox" name="demo_mode"
                                                        checked>
                                                    <label>{up_test_mode}</label>
                                                    <p><em>{up_test_mode_notice}</em></p>
                                                </span>
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