<div class="container-fluid">
    <form name="frm_encrypter" method="POST" action="?page=encrypter">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{et_title}</h1>
        </div>
        <p class="mb-4">{et_sub_title}</p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{et_general}</h6>
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
                                                    {et_pass}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="unencrypted"
                                                    value="{unencrypted}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {et_result}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="encrypted"
                                                    value="{encrypted}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{et_encript}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
            </div>
        </div>
    </form>
</div>