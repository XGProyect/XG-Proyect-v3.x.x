<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseMoons" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true"
        aria-controls="collapseMoons">
        <h6 class="m-0 font-weight-bold text-primary">{moons}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapseMoons" style="">
        <div class="card-body">
            <div class="table-responsive">
                {alert_info}
                <form name="save_info" method="post" action="">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        <tr>
                            <td>{us_user_main_name}</td>
                            <td><input type="text" class="form-control" name="planet_name" value="{planet_name}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_id_owner}</td>
                            <td>
                                <select name="planet_user_id" class="form-control">
                                    {planet_user_id}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_coords}</td>
                            <td>
                                <div class="form-group">
                                    <div class="input-group w-50">
                                        <input class="form-control" name="planet_galaxy" type="number" minlength="1"
                                            maxlength="1" placeholder="1" value="{planet_galaxy}">
                                        <span style="font-size:25.5px">:</span>
                                        <input class="form-control" name="planet_system" type="number" minlength="1"
                                            maxlength="3" placeholder="1" value="{planet_system}">
                                        <span style="font-size:25.5px">:</span>
                                        <input class="form-control" name="planet_planet" type="number" minlength="1"
                                            maxlength="2" placeholder="1" value="{planet_planet}">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_last_update}</td>
                            <td>{planet_last_update}</td>
                        </tr>
                        <tr>
                            <td>{us_user_main_planet_type}</td>
                            <td>
                                <select name="planet_type" class="form-control">
                                    <option value="1" {type1}>{us_user_main_planet}</option>
                                    <option value="3" {type2}>{us_user_main_moon}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_destroyed}</td>
                            <td>
                                <select name="planet_destroyed" class="form-control">
                                    <option value="1" {dest1}>{us_user_main_planet_destroyed_yes}</option>
                                    <option value="2" {dest2}>{us_user_main_planet_destroyed_no}</option>
                                </select>
                                {planet_destroyed}
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_b_building}</td>
                            <td>{planet_b_building}</td>
                        </tr>
                        <tr>
                            <td>{us_user_main_b_building_id}</td>
                            <td>
                                <select name="planet_b_building_id" class="form-control">
                                    {planet_b_building_id}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_b_hangar}</td>
                            <td>{planet_b_hangar}</td>
                        </tr>
                        <tr>
                            <td>{us_user_main_b_hangar_id}</td>
                            <td>
                                <select name="planet_b_hangar_id" class="form-control">
                                    {planet_b_hangar_id}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_image}</td>
                            <td>
                                <select name="planet_image" class="form-control">
                                    {planet_image}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_diameter}</td>
                            <td><input type="text" class="form-control" name="planet_diameter"
                                    value="{planet_diameter}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_field_current}</td>
                            <td><input type="text" class="form-control" name="planet_field_current"
                                    value="{planet_field_current}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_field_max}</td>
                            <td><input type="text" class="form-control" name="planet_field_max"
                                    value="{planet_field_max}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_temp_min}</td>
                            <td><input type="text" class="form-control" name="planet_temp_min"
                                    value="{planet_temp_min}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_temp_max}</td>
                            <td><input type="text" class="form-control" name="planet_temp_max"
                                    value="{planet_temp_max}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_metal}</td>
                            <td><input type="text" class="form-control" name="planet_metal" value="{planet_metal}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_crystal}</td>
                            <td><input type="text" class="form-control" name="planet_crystal" value="{planet_crystal}">
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_deuterium}</td>
                            <td><input type="text" class="form-control" name="planet_deuterium"
                                    value="{planet_deuterium}"></td>
                        </tr>
                        <tr>
                            <td>{us_user_main_ship_solar_satellite_percent}</td>
                            <td>
                                <select name="planet_ship_solar_satellite_percent" class="form-control">
                                    {planet_ship_solar_satellite_percent}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{us_user_main_last_jump_time}</td>
                            <td>
                                <span style="font-size:12px;" class="text-error">{us_user_main_reset}</span> <input
                                    type="checkbox" class="form-input-check" name="planet_last_jump_time">
                                {planet_last_jump_time}
                            </td>
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