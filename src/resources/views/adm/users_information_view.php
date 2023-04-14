<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseInformation" class="d-block card-header py-3" data-toggle="collapse" role="button"
        aria-expanded="true" aria-controls="collapseInformation">
        <h6 class="m-0 font-weight-bold text-primary">{information}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapseInformation" style="">
        <div class="card-body">
            <div class="table-responsive">
                {alert_info}
                <form name="save_info" method="post" action="">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        <tr>
                            <td>{us_user_information_username}</td>
                            <td><input type="text" class="form-control" name="username" value="{user_name}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_information_password}</td>
                            <td><input type="text" class="form-control" name="password" value="" minlength="8"></td>
                        </tr>
                        <tr>
                            <td>{us_user_information_email}</td>
                            <td><input type="text" class="form-control" name="email" value="{user_email}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_information_level}</td>
                            <td>
                                <select name="authlevel" class="form-control">
                                    {user_roles}
                                        <option value="{role_id}" {role_sel}>{role_name}</option>
                                    {/user_roles}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_information_pp}</td>
                            <td>
                                <select name="id_planet" class="form-control">
                                    {main_planet}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_information_ap}</td>
                            <td>
                                <select name="current_planet" class="form-control">
                                    {current_planet}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_information_last_ip}</td>
                            <td>{user_lastip}</td>
                        </tr>
                        <tr>
                            <td>{us_user_information_reg_ip}</td>
                            <td>{user_ip_at_reg}</td>
                        </tr>
                        <tr>
                            <td>{us_user_information_browser}</td>
                            <td>{user_agent}</td>
                        </tr>
                        <tr>
                            <td>{us_user_information_actual_page}</td>
                            <td>{user_current_page}</td>
                        </tr>
                        <tr>
                            <td>{us_user_information_date_reg}</td>
                            <td>{user_register_time}</td>
                        </tr>
                        <tr>
                            <td>{us_user_information_conection}</td>
                            <td>{user_onlinetime}</td>
                        </tr>
                        <tr>
                            <td>{us_user_information_shortcuts}</td>
                            <td>
                                <select name="user_fleet_shortcuts" class="form-control">
                                    {user_fleet_shortcuts}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_information_alliance}</td>
                            <td>
                                <select name="ally_id" class="form-control">
                                    <option value="0">-</option>
                                    {alliances}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_information_banned}</td>
                            <td>{user_banned}</td>
                        </tr>
                    </table>
                    <div class="text-center">
                        <input type="hidden" name="send_data" value="1">
                        <button type="submit" class="btn btn-primary btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-save"></i>
                            </span>
                            <span class="text">{us_send_data}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
