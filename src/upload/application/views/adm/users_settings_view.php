<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseSettings" class="d-block card-header py-3" data-toggle="collapse" role="button"
        aria-expanded="true" aria-controls="collapseSettings">
        <h6 class="m-0 font-weight-bold text-primary">{settings}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapseSettings" style="">
        <div class="card-body">
            <div class="table-responsive">
                {alert_info}
                <form name="save_info" method="post" action="">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        <tr>
                            <td colspan="2">{us_user_settings_general_title}</td>
                        </tr>
                        <tr>
                            <td>{us_user_preference_planet_sort}</td>
                            <td>
                                <select name="preference_planet_sort" class="form-control">
                                    {preference_planet_sort}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_preference_planet_sort_sequence}</td>
                            <td>
                                <select name="preference_planet_sort_sequence" class="form-control">
                                    {preference_planet_sort_sequence}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">{us_user_settings_galaxy_title}</td>
                        </tr>
                        <tr>
                            <td>{us_user_preference_spy_probes}</td>
                            <td><input type="text" class="form-control" name="preference_spy_probes"
                                    value="{preference_spy_probes}"></td>
                        </tr>
                        <tr>
                            <td colspan="2">{us_user_settings_other_title}</td>
                        </tr>
                        <tr>
                            <td>{us_user_preference_vacations_status}</td>
                            <td><input type="checkbox" class="form-input-check" name="preference_vacations_status"
                                    {preference_vacations_status}>
                                <span class="small_font">{preference_vacation_mode}</span></td>
                        </tr>
                        <tr>
                            <td>{us_user_preference_delete_mode}</td>
                            <td><input type="checkbox" class="form-input-check" name="preference_delete_mode"
                                    {preference_delete_mode}></td>
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