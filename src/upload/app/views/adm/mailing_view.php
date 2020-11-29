<div class="container-fluid">
    {alert}
    <form action="" method="POST">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{ma_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{ma_save_changes}</span>
            </button>
        </div>
        <p class="mb-4">{ma_sub_title}</p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{ma_general}</h6>
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
                                                    {ma_mailing_protocol}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-control"  name="mailing_protocol">
                                                    {protocol_options}
                                                    <option value="{value}"{selected}>{option}</option>
                                                    {/protocol_options}
                                                </select>
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
                        <h6 class="m-0 font-weight-bold text-primary">{ma_smtp_title}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseOtherParameters" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td colspan="2">{ma_smtp_warning}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ma_smtp_host}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="mailing_smtp_host"
                                                    value="{mailing_smtp_host}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ma_smtp_user}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                    name="mailing_smtp_user"
                                                    value="{mailing_smtp_user}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ma_smtp_pass}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                    name="mailing_smtp_pass"
                                                    value="{mailing_smtp_pass}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ma_smtp_port}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="mailing_smtp_port"
                                                    value="{mailing_smtp_port}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ma_smtp_timeout}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number"
                                                    name="mailing_smtp_timeout"
                                                    value="{mailing_smtp_timeout}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {ma_smtp_crypto}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="mailing_smtp_crypto">
                                                    {smtp_crypto_options}
                                                    <option value="{value}"{selected}>{option}</option>
                                                    {/smtp_crypto_options}
                                                </select>
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
