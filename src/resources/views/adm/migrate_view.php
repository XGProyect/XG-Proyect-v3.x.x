<div class="container-fluid">
    {alert}
    <form name="migrate_form" method="post" action="">
        <input type="hidden" name="send" value="send">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{mi_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-exchange-alt"></i>
                </span>
                <span class="text">{mi_go}</span>
            </button>
        </div>
        <p class="mb-4">{mi_sub_title}</p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{mi_prev_version_info}</h6>
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
                                                    {mi_version_select}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="version_select">
                                                    <option value="0">{mi_option_init}</option>
                                                    {versions_list}
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {mi_server_title}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="host"
                                                    placeholder="{mi_ex_tag} localhost" value="{v_host}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {mi_user_title}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="user"
                                                    placeholder="{mi_ex_tag} root" value="{v_user}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {mi_password_title}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="password" value=""
                                                    autocomplete="off">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {mi_db_title}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="db"
                                                    placeholder="{mi_ex_tag} xgproyect" value="{v_db}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {mi_prefix_title}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="prefix"
                                                    placeholder="{mi_ex_tag} xgp_" value="{v_prefix}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {mi_test_mode}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{mi_test_mode_notice}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-check-input" type="checkbox" name="demo_mode"
                                                    checked>
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
                    <a href="#collapseAlerts" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseAlerts">
                        <h6 class="m-0 font-weight-bold text-primary">{mi_alert_title}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseAlerts" style="">
                        <div class="card-body">
                            <ul>
                                <li class="text-danger">{mi_alert_type_1_content_1}</li>
                                <li class="text-warning">{mi_alert_type_2_content_1}</li>
                                <li class="text-info">{mi_alert_type_3_content_1}</li>
                                <li class="text-info">{mi_alert_type_3_content_2}</li>
                                <li class="text-info">{mi_alert_type_3_content_3}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>