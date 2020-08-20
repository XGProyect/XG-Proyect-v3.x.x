<div class="container-fluid">
    {alert}
    <form action="" method="POST">
        <input type="hidden" name="opt_save" value="1">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{se_server_parameters}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{se_save_parameters}</span>
            </button>
        </div>
        <p class="mb-4"></p>


        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseServerParameters" class="d-block card-header py-3" data-toggle="collapse"
                        role="button" aria-expanded="true" aria-controls="collapseServerParameters">
                        <h6 class="m-0 font-weight-bold text-primary">{se_server_parameters}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseServerParameters" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_name}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_server_name}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="game_name"
                                                    value="{game_name}" maxlength="60">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_logo}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_server_logo}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="game_logo"
                                                    value="{game_logo}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_lang}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="language">
                                                    {language_settings}
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_general_speed}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_normal_speed}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" name="game_speed" value="{game_speed}"
                                                    type="number" min="1" max="100">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_fleet_speed}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_normal_speed_fleett}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" name="fleet_speed" value="{fleet_speed}"
                                                    type="number" min="1" max="100">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_resources_producion_speed}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_normal_speed_resoruces}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" name="resource_multiplier"
                                                    value="{resource_multiplier}" type="number" min="1" max="100">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_admin_email}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="admin_email" size="60"
                                                    maxlength="254" value="{admin_email}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_forum_link}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="forum_url" size="60"
                                                    maxlength="254" value="{forum_url}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_server_op_close}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-check-input" type="checkbox" name="closed" {closed}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_server_status_message}
                                                </span>
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="close_reason" cols="80" rows="5"
                                                    size="80">{close_reason}</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_date_time_zone}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="date_time_zone">
                                                    {date_time_zone}
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_date_format}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="date_format"
                                                    value="{date_format}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_date_format_extended}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="date_format_extended"
                                                    value="{date_format_extended}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{se_save_parameters}</span>
                                    </button>
                                </div>
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
                        <h6 class="m-0 font-weight-bold text-primary">{se_several_parameters}</h6>
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
                                                    {se_admin_protection}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_title_admins_protection}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input name="adm_attack" {adm_attack} type="checkbox"
                                                    class="form-check-input">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_ships_cdr}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_ships_cdr_message}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <select name="Fleet_Cdr" class="form-control">
                                                    {ships}
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {se_def_cdr}
                                                <i class="fas fa-question-circle" data-toggle="popover"
                                                    data-trigger="hover" data-content="{se_def_cdr_message}"
                                                    data-html="true"></i>
                                            </td>
                                            <td>
                                                <select name="Defs_Cdr" class="form-control">
                                                    {defenses}
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseNoobProtection" class="d-block card-header py-3" data-toggle="collapse"
                        role="button" aria-expanded="true" aria-controls="collapseNoobProtection">
                        <h6 class="m-0 font-weight-bold text-primary">{se_noob_protect}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseNoobProtection" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>{se_noob_protect_active}</td>
                                            <td>
                                                <input name="noobprotection" {noobprot} type="checkbox"
                                                    class="form-check-input">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_noob_protect2}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_noob_protect_e2}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input name="noobprotectiontime" value="{noobprot2}" type="number"
                                                    max="999999999" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {se_noob_protect3}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{se_noob_protect_e3}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input name="noobprotectionmulti" value="{noobprot3}" type="number"
                                                    max="99" class="form-control">
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
