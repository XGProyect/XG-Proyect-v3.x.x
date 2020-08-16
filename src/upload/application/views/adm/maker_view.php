<div class="container-fluid">
    {alert}
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{mk_title}</h1>
    </div>
    <p class="mb-4">{mk_sub_title}</p>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseMakeUser" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseMakeUser">
                    <h6 class="m-0 font-weight-bold text-primary">{mk_user_title}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse" id="collapseMakeUser" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form name="frm_adduser" action="" method="POST">
                                <input type="hidden" name="add_user" value="1">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{mk_user_name}</th>
                                        <td><input class="form-control" type="text" name="name" minlength="4" maxlength="20"></th>
                                    </tr>
                                    <tr>
                                        <td>{mk_user_pass}</th>
                                        <td>
                                            <input class="form-control" type="password" name="password" minlength="8">
                                            <input class="form-input-check" type="checkbox" checked="checked"
                                                name="password_check"> {mk_user_password_random}
                                            </th>
                                    </tr>
                                    <tr>
                                        <td>{mk_user_email}</th>
                                        <td><input class="form-control" type="text" name="email"></td>
                                    </tr>
                                    <tr>
                                        <td>{mk_user_level}</td>
                                        <td>
                                            <select class="form-control" name="authlevel">
                                                {user_levels}
                                                <option value="{id}">{name}</option>
                                                {/user_levels}
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_user_coords}</td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group w-50">
                                                    <input class="form-control" name="galaxy" type="number"
                                                        minlength="1" maxlength="1" placeholder="1">
                                                    <span style="font-size:25.5px">:</span>
                                                    <input class="form-control" name="system" type="number"
                                                        minlength="1" maxlength="3" placeholder="1">
                                                    <span style="font-size:25.5px">:</span>
                                                    <input class="form-control" name="planet" type="number"
                                                        minlength="1" maxlength="2" placeholder="1">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{mk_user_add_user}</span>
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
                <a href="#collapseMakeAlliance" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseMakeAlliance">
                    <h6 class="m-0 font-weight-bold text-primary">{mk_alliance_title}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse" id="collapseMakeAlliance" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form name="frm_addalliance" action="" method="POST">
                                <input type="hidden" name="add_alliance" value="1">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{mk_alliance_name}</th>
                                        <td><input class="form-control" type="text" name="name"></th>
                                    </tr>
                                    <tr>
                                        <td>{mk_alliance_tag}</th>
                                        <td><input class="form-control" type="text" name="tag"></th>
                                    </tr>
                                    <tr>
                                        <td>{mk_alliance_founder}</td>
                                        <td>
                                            <select class="form-control" name="founder">
                                                <option value="0">-</option>
                                                {founders_combo}
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{mk_alliance_add_alliance}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseMakePlanet" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseMakePlanet">
                    <h6 class="m-0 font-weight-bold text-primary">{mk_planet_title}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse" id="collapseMakePlanet" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form name="frm_addplanet" action="" method="POST">
                                <input type="hidden" name="add_planet" value="1">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{mk_planet_user}</td>
                                        <td>
                                            <select class="form-control" name="user">
                                                <option value="0">-</option>
                                                {users_combo}
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_planet_name}</td>
                                        <td>
                                            <input class="form-control" name="name" type="text" maxlength="25"
                                                value="{mk_planet_default_name}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_planet_available_fields}</td>
                                        <td>
                                            <input class="form-control" name="planet_field_max" type="number"
                                                maxlength="3" value="163">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_planet_coords}</td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group w-50">
                                                    <input class="form-control" name="galaxy" type="number"
                                                        minlength="1" maxlength="1" placeholder="1">
                                                    <span style="font-size:25.5px">:</span>
                                                    <input class="form-control" name="system" type="number"
                                                        minlength="1" maxlength="3" placeholder="1">
                                                    <span style="font-size:25.5px">:</span>
                                                    <input class="form-control" name="planet" type="number"
                                                        minlength="1" maxlength="2" placeholder="1">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{mk_planet_add_planet}</span>
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
                <a href="#collapseMakeMoon" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseMakeMoon">
                    <h6 class="m-0 font-weight-bold text-primary">{mk_moon_title}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse" id="collapseMakeMoon" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form name="frm_addmoon" action="" method="POST">
                                <input type="hidden" name="add_moon" value="1">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{mk_moon_planet}</td>
                                        <td>
                                            <select class="form-control" name="planet">
                                                <option value="0">-</option>
                                                {planets_combo}
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_moon_name}</td>
                                        <td>
                                            <input class="form-control" type="text" value="{mk_moon_default_name}"
                                                name="name">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_moon_diameter}</td>
                                        <td>
                                            <input class="form-control" type="number" name="planet_diameter"
                                                maxlength="5">
                                            <input class="form-input-check" type="checkbox" checked="checked"
                                                name="diameter_check"> {mk_moon_random}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_moon_temperature}</td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group w-50">
                                                    <input class="form-control" name="planet_temp_min" type="number">
                                                    <span style="font-size:25.5px">/</span>
                                                    <input class="form-control" name="planet_temp_max" type="number">
                                                </div>
                                            </div>
                                            <input class="form-input-check" type="checkbox" checked="checked"
                                                name="temp_check"> {mk_moon_random}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{mk_moon_available_fields}</td>
                                        <td>
                                            <input class="form-control" type="number" name="planet_field_max"
                                                maxlength="5" value="1">
                                        </td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{mk_moon_add_moon}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
